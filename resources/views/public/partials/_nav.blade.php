@php
    $branch = \App\Models\Branch::where('is_active', true)->first() ?? \App\Models\Branch::first();
    $companyName = $branch->name ?? config('app.name', 'Elite Locadora');
    $companyPhone = $branch->phone ?? null;
    $companyWhatsapp = $branch->whatsapp ?? $companyPhone;
    $companyEmail = $branch->email ?? null;
    $companyAddress = $branch
        ? trim(collect([
            $branch->address_street,
            $branch->address_number,
            $branch->address_neighborhood,
            ($branch->address_city && $branch->address_state) ? $branch->address_city.'/'.$branch->address_state : null,
        ])->filter()->implode(', '))
        : null;
    $whatsappLink = $companyWhatsapp
        ? 'https://wa.me/55'.preg_replace('/\D/', '', $companyWhatsapp)
        : null;
@endphp
