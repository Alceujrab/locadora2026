<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Enums\ContractStatus;
use App\Enums\InspectionType;
use App\Services\ContractService;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class ContractActionController extends Controller
{
    /**
     * Check-out (Entrega do veículo ao cliente).
     * Ativa o contrato, cria vistoria de saída.
     */
    public function checkout(string $id)
    {
        $contract = Contract::findOrFail($id);

        if (!in_array($contract->status, [ContractStatus::DRAFT, ContractStatus::AWAITING_SIGNATURE])) {
            return response()->json([
                'message' => 'Contrato não está em rascunho ou aguardando assinatura.',
            ], 422);
        }

        $inspection = \App\Models\VehicleInspection::firstOrCreate([
            'contract_id' => $contract->id,
            'type' => InspectionType::CHECKOUT,
        ], [
            'vehicle_id' => $contract->vehicle_id,
            'inspector_user_id' => auth()->id() ?? 1,
            'status' => 'rascunho',
            'inspection_date' => now(),
            'mileage' => $contract->vehicle->mileage ?? 0,
            'fuel_level' => 100,
            'overall_condition' => 'Bom',
        ]);

        $contract->update(['status' => ContractStatus::ACTIVE]);

        // Atualiza reserva se existir
        if ($contract->reservation) {
            $contract->reservation->update(['status' => \App\Enums\ReservationStatus::IN_PROGRESS]);
        }

        // Atualiza veículo para "em uso"
        $contract->vehicle->update(['status' => \App\Enums\VehicleStatus::RENTED]);

        return response()->json([
            'message' => 'Check-out realizado com sucesso! Contrato ativado.',
            'messageType' => 'success',
        ]);
    }

    /**
     * Check-in (Devolução do veículo pelo cliente).
     * Finaliza o contrato, cria vistoria de retorno, libera veículo.
     */
    public function checkin(string $id)
    {
        $contract = Contract::findOrFail($id);

        if ($contract->status !== ContractStatus::ACTIVE) {
            return response()->json([
                'message' => 'Apenas contratos ativos podem receber check-in.',
            ], 422);
        }

        $inspection = \App\Models\VehicleInspection::firstOrCreate([
            'contract_id' => $contract->id,
            'type' => InspectionType::RETURN,
        ], [
            'vehicle_id' => $contract->vehicle_id,
            'inspector_user_id' => auth()->id() ?? 1,
            'status' => 'rascunho',
            'inspection_date' => now(),
            'mileage' => $contract->vehicle->mileage ?? $contract->pickup_mileage ?? 0,
            'fuel_level' => 100,
            'overall_condition' => 'Bom',
        ]);

        $contract->update([
            'status' => ContractStatus::FINISHED,
            'actual_return_date' => now(),
        ]);

        // Atualiza reserva se existir
        if ($contract->reservation) {
            $contract->reservation->update(['status' => \App\Enums\ReservationStatus::COMPLETED]);
        }

        // Libera veículo
        $contract->vehicle->update(['status' => \App\Enums\VehicleStatus::AVAILABLE]);

        return response()->json([
            'message' => 'Check-in realizado com sucesso! Veículo liberado.',
            'messageType' => 'success',
        ]);
    }

    /**
     * Gerar PDF do contrato a partir do template.
     */
    public function generatePdf(string $id, ContractService $service)
    {
        $contract = Contract::findOrFail($id);

        if (!$contract->template_id) {
            return response()->json([
                'message' => 'Nenhum template selecionado neste contrato.',
                'messageType' => 'error',
            ], 422);
        }

        $result = $service->generatePdf($contract);

        if ($result) {
            return response()->json([
                'message' => 'PDF do contrato gerado com sucesso!',
                'messageType' => 'success',
            ]);
        }

        return response()->json([
            'message' => 'Erro ao gerar PDF: O template pode estar vazio.',
            'messageType' => 'error',
        ], 500);
    }

    /**
     * Gerar Fatura(s) para o contrato.
     */
    public function generateInvoices(string $id, InvoiceService $service)
    {
        $contract = Contract::findOrFail($id);

        if (!in_array($contract->status, [ContractStatus::ACTIVE, ContractStatus::FINISHED])) {
            return response()->json([
                'message' => 'Apenas contratos ativos ou finalizados podem gerar faturas.',
                'messageType' => 'error',
            ], 422);
        }

        if ($contract->invoices()->exists()) {
            return response()->json([
                'message' => 'Este contrato já possui faturas geradas.',
                'messageType' => 'error',
            ], 422);
        }

        $invoices = $service->generateForContract($contract, 1, 5);

        if (count($invoices) > 0) {
            return response()->json([
                'message' => 'Fatura gerada com sucesso! (R$ ' . number_format((float)$contract->total, 2, ',', '.') . ')',
                'messageType' => 'success',
            ]);
        }

        return response()->json([
            'message' => 'Erro ao gerar fatura.',
            'messageType' => 'error',
        ], 500);
    }
}
