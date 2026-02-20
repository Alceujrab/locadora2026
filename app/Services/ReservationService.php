<?php

namespace App\Services;

use App\Models\VehicleCategory;
use App\Models\Vehicle;
use App\Models\RentalExtra;
use Carbon\Carbon;

class ReservationService
{
    /**
     * Calcula o planejamento de valores (diárias, extras e totais) para uma reserva.
     * 
     * @param string|Carbon $pickupDate Data/Hora de retirada
     * @param string|Carbon $returnDate Data/Hora de devolução
     * @param int|null $vehicleId Veículo específico da reserva
     * @param int|null $categoryId Categoria da reserva (fallback quando não há veículo)
     * @param array $extraIds IDs dos extras escolhidos
     * @param float $discount Valor de desconto
     * @return array
     */
    public function calculatePricing(
        string|Carbon $pickupDate, 
        string|Carbon $returnDate, 
        ?int $vehicleId = null, 
        ?int $categoryId = null, 
        array $extraIds = [], 
        float $discount = 0.0
    ): array {
        $pickup = Carbon::parse($pickupDate);
        $return = Carbon::parse($returnDate);
        
        // Em locadoras, a diária costuma ser contabilizada a cada 24h, com flexibilidade.
        // Simulando a cobrança simples por dias completos (mínimo 1 dia).
        $diffHours = $pickup->diffInHours($return);
        $totalDays = max(1, ceil($diffHours / 24));
        
        $dailyRate = 0.0;
        
        if ($vehicleId) {
            $vehicle = Vehicle::with('category')->find($vehicleId);
            if ($vehicle) {
                $dailyRate = (float)$vehicle->daily_rate;
            }
        } elseif ($categoryId) {
            $category = VehicleCategory::find($categoryId);
            if ($category) {
                $dailyRate = (float)$category->daily_rate;
            }
        }
        
        $subtotal = $totalDays * $dailyRate;
        
        $extrasTotal = 0.0;
        if (!empty($extraIds)) {
            $extras = RentalExtra::whereIn('id', $extraIds)->get();
            foreach ($extras as $extra) {
                // Taxa do Extra x Dias
                $extrasTotal += ((float)$extra->daily_rate * $totalDays);
            }
        }
        
        $grandTotal = ($subtotal + $extrasTotal) - $discount;
        
        return [
            'total_days' => $totalDays,
            'daily_rate' => $dailyRate,
            'subtotal' => round($subtotal, 2),
            'extras_total' => round($extrasTotal, 2),
            'discount' => round($discount, 2),
            'total' => max(0, round($grandTotal, 2)), // Impede valor negativo
        ];
    }
}
