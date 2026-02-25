<?php

namespace App\Http\Controllers;

use App\Enums\CustomerType;
use App\Models\Customer;
use App\Models\RentalExtra;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CheckoutController extends Controller
{
    /**
     * Passo 1: Seleção de Opcionais e Extras
     */
    public function extras(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start' => 'required|date|after_or_equal:today',
            'end' => 'required|date|after:start',
        ]);

        $vehicle = Vehicle::with('category')->findOrFail($request->vehicle_id);

        // Verifica se o veículo está disponível no período (Opcional, mas recomendado adicionar uma query de conflito aqui futuramente se necessário)
        if ($vehicle->status->value !== 'disponivel') {
            return redirect()->back()->with('error', 'O veículo não está disponível.');
        }

        // Buscar todos os extras ativos, agrupados por tipo (se desejado, vamos passar tudo por enquanto)
        $extras = RentalExtra::orderBy('name')->get();

        // Calcular número de dias
        $startDate = Carbon::parse($request->start);
        $endDate = Carbon::parse($request->end);
        $days = $startDate->diffInDays($endDate);
        if ($days === 0) {
            $days = 1;
        } // Mínimo 1 diária

        $dailyRate = $vehicle->category->daily_rate ?? 0;
        $vehicleTotal = $dailyRate * $days;

        // Salvar dados Iniciais na sessão para o processo de Checkout
        $reservationData = [
            'vehicle_id' => $vehicle->id,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'days' => $days,
            'daily_rate' => $dailyRate,
            'vehicle_total' => $vehicleTotal,
            'selected_extras' => [],
        ];

        $request->session()->put('checkout_reservation', $reservationData);

        return view('public.checkout.extras', compact('vehicle', 'extras', 'days', 'vehicleTotal', 'startDate', 'endDate'));
    }

    /**
     * Passo 2: Processar Opcionais e ir para Identificação
     */
    public function processExtras(Request $request)
    {
        $request->validate([
            'extras' => 'nullable|array',
            'extras.*' => 'exists:rental_extras,id',
        ]);

        $reservationData = $request->session()->get('checkout_reservation');
        if (! $reservationData) {
            return redirect()->route('public.vehicles')->with('error', 'Sessão expirada. Por favor, reinicie a reserva.');
        }

        // Calcular total dos extras
        $totalExtras = 0;
        $selectedExtrasDetails = [];

        if ($request->has('extras')) {
            $selectedExtrasDB = RentalExtra::whereIn('id', $request->extras)->get();
            foreach ($selectedExtrasDB as $extra) {
                // Se o extra for cobrado 'por_dia', multiplica pelos dias da reserva
                $itemTotal = $extra->charge_type === 'por_dia' ? ($extra->price * $reservationData['days']) : $extra->price;
                $totalExtras += $itemTotal;

                $selectedExtrasDetails[] = [
                    'id' => $extra->id,
                    'name' => $extra->name,
                    'price' => $extra->price,
                    'charge_type' => $extra->charge_type,
                    'total' => $itemTotal,
                ];
            }
        }

        $reservationData['selected_extras'] = $selectedExtrasDetails;
        $reservationData['extras_total'] = $totalExtras;
        $reservationData['grand_total'] = $reservationData['vehicle_total'] + $totalExtras;

        $request->session()->put('checkout_reservation', $reservationData);

        return redirect()->route('checkout.identify');
    }

    /**
     * Passo 2: Exibir Identificação (Login ou Cadastro)
     */
    public function identify(Request $request)
    {
        $reservationData = $request->session()->get('checkout_reservation');
        if (! $reservationData) {
            return redirect()->route('public.vehicles')->with('error', 'Sessão expirada. Por favor, reinicie a reserva.');
        }

        // Se já está logado como cliente, pula direto pro Passo 3
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->hasRole('cliente')) {
            return redirect()->route('checkout.confirm');
        }

        return view('public.checkout.identify', [
            'vehicleTotal' => $reservationData['vehicle_total'],
            'extrasTotal' => $reservationData['extras_total'] ?? 0,
            'grandTotal' => $reservationData['grand_total'] ?? $reservationData['vehicle_total'],
        ]);
    }

    /**
     * Passo 2b: Processar Login Rápido
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::guard('web')->user();

            if (! $user->hasRole('cliente')) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->with('error', 'Acesso negado. Apenas clientes podem reservar.');
            }

            return redirect()->route('checkout.confirm');
        }

        return back()->with('error', 'Credenciais inválidas.');
    }

    /**
     * Passo 2c: Processar Cadastro Expresso
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf_cnpj' => 'required|string|max:20|unique:customers,cpf_cnpj',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Criar Usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('cliente');

        // Criar Cliente
        $customer = Customer::create([
            'user_id' => $user->id,
            'type' => CustomerType::PF,
            'name' => $request->name,
            'cpf_cnpj' => preg_replace('/[^0-9]/', '', $request->cpf_cnpj),
            'email' => $request->email,
            'phone' => preg_replace('/[^0-9]/', '', $request->phone),
            'is_blocked' => false,
        ]);

        // Autenticar
        Auth::guard('web')->login($user);

        return redirect()->route('checkout.confirm');
    }

    /**
     * Passo 3: Exibir Resumo e Confirmação
     */
    public function confirm(Request $request)
    {
        $reservationData = $request->session()->get('checkout_reservation');
        if (! $reservationData) {
            return redirect()->route('public.vehicles')->with('error', 'Sessão expirada. Por favor, reinicie a reserva.');
        }

        if (! Auth::guard('web')->check() || ! Auth::guard('web')->user()->hasRole('cliente')) {
            return redirect()->route('checkout.identify');
        }

        $vehicle = Vehicle::with('category')->findOrFail($reservationData['vehicle_id']);
        $customer = Customer::where('user_id', Auth::guard('web')->id())->firstOrFail();

        return view('public.checkout.confirm', [
            'reservationData' => $reservationData,
            'vehicle' => $vehicle,
            'customer' => $customer,
        ]);
    }

    /**
     * Passo 3b: Processar a Criação da Reserva
     */
    public function finish(Request $request)
    {
        $reservationData = $request->session()->get('checkout_reservation');
        if (! $reservationData) {
            return redirect()->route('public.vehicles')->with('error', 'Sessão expirada. Por favor, reinicie a reserva.');
        }

        if (! Auth::guard('web')->check() || ! Auth::guard('web')->user()->hasRole('cliente')) {
            return redirect()->route('checkout.identify');
        }

        $customer = Customer::where('user_id', Auth::guard('web')->id())->firstOrFail();
        $vehicle = Vehicle::with('category', 'branch')->findOrFail($reservationData['vehicle_id']);

        if ($vehicle->status->value !== 'disponivel') {
            return redirect()->route('public.vehicles')->with('error', 'O veículo selecionado não está mais disponível.');
        }

        // 1. Criar a Reserva
        $reservation = new \App\Models\Reservation;
        $reservation->branch_id = $vehicle->branch_id ?? 1; // Fallback se o veículo não tiver filial
        $reservation->customer_id = $customer->id;
        $reservation->vehicle_id = $vehicle->id;
        $reservation->status = \App\Enums\ReservationStatus::PENDENTE;

        $reservation->pickup_date = Carbon::parse($reservationData['start_date']);
        $reservation->return_date = Carbon::parse($reservationData['end_date']);
        $reservation->pickup_location = $vehicle->branch->name ?? 'Loja Principal';
        $reservation->return_location = $vehicle->branch->name ?? 'Loja Principal';

        $reservation->daily_rate = $reservationData['daily_rate'];
        $reservation->rental_days = $reservationData['days'];
        $reservation->daily_total = $reservationData['vehicle_total'];

        $reservation->extras_total = $reservationData['extras_total'] ?? 0;
        $reservation->discount_amount = 0;
        $reservation->total_amount = $reservationData['grand_total'];
        $reservation->deposit_amount = 1000.00; // Exemplo de Caução Padrão (Pode vir de Setting)
        $reservation->notes = 'Reserva gerada pelo portal do cliente.';

        $reservation->save();

        // 2. Salvar Extras da Reserva
        if (! empty($reservationData['selected_extras'])) {
            foreach ($reservationData['selected_extras'] as $extra) {
                // Check if extra is per day
                $qty = $extra['charge_type'] === 'por_dia' ? $reservationData['days'] : 1;

                \App\Models\ReservationExtra::create([
                    'reservation_id' => $reservation->id,
                    'rental_extra_id' => $extra['id'],
                    'quantity' => $qty,
                    'unit_price' => $extra['price'],
                    'total' => $extra['total'],
                ]);
            }
        }

        // Limpar a sessão
        $request->session()->forget('checkout_reservation');

        // Em uma implementação real, poderíamos disparar um Evento que enviaria notificação de WhatsApp (Observer)
        return redirect()->route('cliente.reservations')->with('success', 'Sua reserva foi solicitada com sucesso! Nossa equipe entrará em contato em breve para confirmação.');
    }
}
