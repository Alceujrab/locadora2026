<?php

namespace App\Http\Controllers;

use App\Models\FineTraffic;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class FineDriverIdentificationController extends Controller
{
    /**
     * Gera o FICI — Formulário de Identificação do Condutor Infrator
     * no padrão CONTRAN Res. 918/2022 (substituta da 404/2012),
     * mesclando a CNH e o Comprovante de Endereço ao final (PDFs ou imagens).
     */
    public function download(int $id)
    {
        $fine = FineTraffic::with(['vehicle', 'customer', 'contract.customer'])->findOrFail($id);

        if (empty($fine->driver_name) || empty($fine->driver_cpf)) {
            abort(422, 'Preencha os dados do condutor informado antes de gerar o FICI.');
        }

        $logoBase64 = null;
        $logoPath = public_path('images/logo-elite.png');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $company = [
            'name'    => Setting::get('company_name',    'Elite Locadora de Veículos'),
            'cnpj'    => Setting::get('company_cnpj',    ''),
            'phone'   => Setting::get('company_phone',   ''),
            'email'   => Setting::get('company_email',   ''),
            'address' => Setting::get('company_address', ''),
            'city'    => Setting::get('company_city',    ''),
            'state'   => Setting::get('company_state',   ''),
            'zip'     => Setting::get('company_zip',     ''),
        ];

        $ficiPdfBytes = Pdf::loadView('pdf.fine-driver-identification', [
            'fine'       => $fine,
            'company'    => $company,
            'logoBase64' => $logoBase64,
        ])->setPaper('a4', 'portrait')->output();

        $plate = $fine->vehicle?->plate ?? 'VEICULO';
        $ait   = $fine->auto_infraction_number ?: $fine->id;
        $filename = "FICI_{$plate}_{$ait}.pdf";

        // Anexos do condutor: CNH + Comprovante de Endereco
        $attachments = array_values(array_filter([
            ['path' => $fine->driver_cnh_path,           'title' => 'Copia da CNH / Documento de Identificacao'],
            ['path' => $fine->driver_address_proof_path, 'title' => 'Comprovante de Endereco do Condutor'],
        ], fn ($a) => !empty($a['path']) && Storage::disk('public')->exists($a['path'])));

        $finalPdf = empty($attachments)
            ? $ficiPdfBytes
            : $this->mergeFiciWithAttachments($ficiPdfBytes, $attachments);

        return response($finalPdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Mescla o PDF do FICI com os anexos (PDFs e imagens) usando FPDI/FPDF.
     *
     * @param  string  $ficiPdfBytes  Bytes do PDF do FICI gerado pelo DomPDF.
     * @param  array<int, array{path: string, title: string}>  $attachments
     * @return string  Bytes do PDF final mesclado.
     */
    protected function mergeFiciWithAttachments(string $ficiPdfBytes, array $attachments): string
    {
        $tmpDir = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'fici_' . uniqid();
        @mkdir($tmpDir, 0777, true);

        $ficiPath = $tmpDir . DIRECTORY_SEPARATOR . 'fici.pdf';
        file_put_contents($ficiPath, $ficiPdfBytes);

        $tempImages = [];

        try {
            $pdf = new Fpdi('P', 'mm', 'A4');
            $pdf->SetCreator('Elite Locadora');
            $pdf->SetAuthor('Elite Locadora');
            $pdf->SetTitle('FICI com anexos');

            // 1) Paginas do FICI
            $this->importPdfPages($pdf, $ficiPath);

            // 2) Para cada anexo: capa + conteudo (PDF importado ou imagem)
            foreach ($attachments as $att) {
                $absPath = Storage::disk('public')->path($att['path']);
                if (!is_file($absPath)) {
                    continue;
                }

                $ext = strtolower(pathinfo($absPath, PATHINFO_EXTENSION));

                $this->addAttachmentCover($pdf, $att['title']);

                if ($ext === 'pdf') {
                    $this->importPdfPages($pdf, $absPath);
                } elseif (in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
                    $this->addImagePage($pdf, $absPath);
                } elseif ($ext === 'webp') {
                    $jpgPath = $tmpDir . DIRECTORY_SEPARATOR . uniqid('att_') . '.jpg';
                    if ($this->convertWebpToJpg($absPath, $jpgPath)) {
                        $tempImages[] = $jpgPath;
                        $this->addImagePage($pdf, $jpgPath);
                    }
                }
            }

            return $pdf->Output('S');
        } finally {
            @unlink($ficiPath);
            foreach ($tempImages as $f) {
                @unlink($f);
            }
            @rmdir($tmpDir);
        }
    }

    protected function importPdfPages(Fpdi $pdf, string $path): void
    {
        $pageCount = $pdf->setSourceFile($path);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $pdf->importPage($i);
            $size  = $pdf->getTemplateSize($tplId);
            $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';
            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId);
        }
    }

    protected function addAttachmentCover(Fpdi $pdf, string $title): void
    {
        $pdf->AddPage('P', 'A4');
        $pdf->SetFont('Helvetica', 'B', 14);
        $pdf->SetY(20);
        $pdf->Cell(0, 10, utf8_decode('ANEXO'), 0, 1, 'C');
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Ln(2);
        $pdf->MultiCell(0, 8, utf8_decode($title), 0, 'C');
        $pdf->Ln(4);
        $pdf->SetDrawColor(180, 180, 180);
        $pdf->Line(20, $pdf->GetY(), 190, $pdf->GetY());
    }

    protected function addImagePage(Fpdi $pdf, string $imagePath): void
    {
        $info = @getimagesize($imagePath);
        if (!$info) {
            return;
        }

        [$iw, $ih] = $info;
        $pdf->AddPage('P', 'A4');

        // A4 = 210 x 297 mm. Margem util ~15mm.
        $maxW = 180;
        $maxH = 260;
        $ratio = min($maxW / $iw, $maxH / $ih);
        $w = $iw * $ratio;
        $h = $ih * $ratio;
        $x = (210 - $w) / 2;
        $y = 20;

        $pdf->Image($imagePath, $x, $y, $w, $h);
    }

    protected function convertWebpToJpg(string $src, string $dst): bool
    {
        if (!function_exists('imagecreatefromwebp') || !function_exists('imagejpeg')) {
            return false;
        }
        $im = @imagecreatefromwebp($src);
        if (!$im) {
            return false;
        }
        $ok = imagejpeg($im, $dst, 90);
        imagedestroy($im);
        return $ok;
    }
}
