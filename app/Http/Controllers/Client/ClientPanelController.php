<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientPanelController extends Controller
{
    /**
     * Dashboard Principal do Cliente
     */
    public function dashboard()
    {
        $user = Auth::guard('web')->user();
        $customer = $user->customer;
        
        if (!$customer) {
            abort(403, 'Registro de cliente não encontrado.');
        }

        // Resumo de dados
        $activeContractsCount = $customer->contracts()->whereIn('status', ['active', 'em_andamento'])->count();
        $pendingInvoicesCount = $customer->invoices()->where('status', 'open')->count();
        $openTicketsCount = $customer->supportTickets()->where('status', 'aberto')->count();

        // Próximas reservas
        $upcomingReservations = $customer->reservations()
            ->whereIn('status', ['confirmed', 'pendent'])
            ->where('pickup_date', '>=', now())
            ->orderBy('pickup_date', 'asc')
            ->take(3)
            ->get();

        return view('client.dashboard', compact(
            'customer', 
            'activeContractsCount', 
            'pendingInvoicesCount', 
            'openTicketsCount',
            'upcomingReservations'
        ));
    }

    /**
     * Minhas Faturas
     */
    public function invoices()
    {
        $customer = Auth::guard('web')->user()->customer;
        $invoices = $customer->invoices()->with('contract.vehicle')->orderBy('due_date', 'desc')->get();
        return view('client.invoices', compact('invoices'));
    }

    /**
     * Meus Contratos
     */
    public function contracts()
    {
        $customer = Auth::guard('web')->user()->customer;
        $contracts = $customer->contracts()->with('vehicle')->orderBy('created_at', 'desc')->get();
        return view('client.contracts', compact('contracts'));
    }

    /**
     * Minhas Reservas
     */
    public function reservations()
    {
        $customer = Auth::guard('web')->user()->customer;
        $reservations = $customer->reservations()->with(['vehicle', 'pickupBranch'])->orderBy('pickup_date', 'desc')->get();
        return view('client.reservations', compact('reservations'));
    }

    /**
     * Suporte e Chamados
     */
    public function support()
    {
        $customer = Auth::guard('web')->user()->customer;
        $tickets = $customer->supportTickets()->orderBy('created_at', 'desc')->get();
        return view('client.support', compact('tickets'));
    }
}
