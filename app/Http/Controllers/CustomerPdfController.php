<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerPdfController extends Controller
{
    public function __invoke(string $id)
    {
        $customer = Customer::with(['branch'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.customer.pdf', compact('customer'))
            ->setPaper('a4', 'portrait');

        $filename = 'ficha_cliente_'.str_replace([' ', '/'], '_', $customer->name).'.pdf';

        return $pdf->stream($filename);
    }
}
