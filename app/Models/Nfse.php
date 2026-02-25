<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nfse extends Model
{
    use SoftDeletes;

    protected $table = 'nfse';

    protected $fillable = [
        'invoice_id', 'numero', 'serie', 'data_emissao',
        'valor_servico', 'aliquota_iss', 'valor_iss',
        'codigo_servico', 'discriminacao',
        'tomador_cnpj_cpf', 'tomador_nome', 'tomador_endereco', 'tomador_email',
        'status', 'xml_path', 'pdf_path', 'observacoes',
    ];

    protected $casts = [
        'data_emissao' => 'date',
        'valor_servico' => 'decimal:2',
        'aliquota_iss' => 'decimal:2',
        'valor_iss' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Auto-generate next NFS-e number
     */
    public static function generateNumero(): string
    {
        $last = self::max('numero');
        $next = $last ? ((int) $last + 1) : 1;

        return str_pad((string) $next, 8, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate ISS value based on service amount and rate
     */
    public function calculateIss(): float
    {
        return round($this->valor_servico * ($this->aliquota_iss / 100), 2);
    }
}
