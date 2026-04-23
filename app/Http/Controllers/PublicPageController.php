<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    public function home()
    {
        $featuredVehicles = Vehicle::with(['category', 'photos'])
            ->where('status', 'disponivel')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        $categories = VehicleCategory::orderBy('name')->get();
        $brands = Vehicle::where('status', 'disponivel')->distinct()->pluck('brand')->filter()->sort();

        $faqs = Faq::where('is_active', true)->orderBy('position')->get();
        $testimonials = Testimonial::where('is_active', true)->orderBy('id', 'desc')->get();

        return view('public.home', compact('featuredVehicles', 'categories', 'brands', 'faqs', 'testimonials'));
    }

    public function vehicles(Request $request)
    {
        $query = Vehicle::with(['category', 'photos'])
            ->where('status', 'disponivel');

        // Filter by Category
        if ($request->has('category_id') && $request->filled('category_id')) {
            $query->where('vehicle_category_id', $request->category_id);
        }

        // Filter by Brand
        if ($request->has('brand') && $request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        // Filter by Transmission
        if ($request->has('transmission') && $request->filled('transmission')) {
            $query->where('transmission', 'like', "%{$request->transmission}%");
        }

        // Filter by Max Price (joined with category)
        if ($request->has('price_max') && $request->filled('price_max')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('daily_rate', '<=', $request->price_max);
            });
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    // Need to join category to sort by price
                    $query->join('vehicle_categories', 'vehicles.vehicle_category_id', '=', 'vehicle_categories.id')
                        ->orderBy('vehicle_categories.daily_rate', 'asc')
                        ->select('vehicles.*');
                    break;
                case 'price_desc':
                    $query->join('vehicle_categories', 'vehicles.vehicle_category_id', '=', 'vehicle_categories.id')
                        ->orderBy('vehicle_categories.daily_rate', 'desc')
                        ->select('vehicles.*');
                    break;
                case 'recent':
                default:
                    $query->orderBy('vehicles.id', 'desc');
                    break;
            }
        } else {
            $query->orderBy('vehicles.id', 'desc');
        }

        $vehicles = $query->paginate(12);

        // Data for Filters
        $categories = VehicleCategory::orderBy('name')->get();
        $brands = Vehicle::where('status', 'disponivel')->distinct()->pluck('brand')->filter()->sort();
        $maxPrice = VehicleCategory::max('daily_rate') ?? 1000;

        return view('public.vehicles.index', compact('vehicles', 'categories', 'brands', 'maxPrice'));
    }

    public function vehicleDetails($id)
    {
        $vehicle = Vehicle::with(['category', 'photos', 'accessories'])->findOrFail($id);

        // Similar vehicles from the same category
        $relatedVehicles = Vehicle::with(['category', 'photos'])
            ->where('status', 'disponivel')
            ->where('category_id', $vehicle->category_id)
            ->where('id', '!=', $vehicle->id)
            ->take(3)
            ->get();

        return view('public.vehicles.show', compact('vehicle', 'relatedVehicles'));
    }

    public function about()
    {
        $testimonials = Testimonial::where('is_active', true)->orderBy('id', 'desc')->take(3)->get();
        $stats = [
            'vehicles' => Vehicle::count(),
            'customers' => \App\Models\Customer::count(),
            'rentals' => \App\Models\Contract::count(),
            'cities' => \App\Models\Branch::where('is_active', true)->distinct('address_city')->count('address_city'),
        ];

        return view('public.about', compact('testimonials', 'stats'));
    }

    public function contact()
    {
        return view('public.contact');
    }

    public function contactSubmit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:120',
            'phone' => 'nullable|string|max:20',
            'subject' => 'nullable|string|max:120',
            'message' => 'required|string|max:2000',
        ]);

        try {
            \App\Models\SupportTicket::create([
                'subject' => $data['subject'] ?? 'Contato pelo site',
                'description' => "Nome: {$data['name']}\nEmail: {$data['email']}\nTelefone: ".($data['phone'] ?? '-')."\n\n".$data['message'],
                'status' => 'aberto',
                'priority' => 'normal',
                'category' => 'contato_site',
            ]);
        } catch (\Throwable $e) {
            // falha silenciosa para nao quebrar a UX publica
            \Log::warning('Falha ao criar ticket de contato: '.$e->getMessage());
        }

        return back()->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }
}
