<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\VehicleCategory;

class PublicPageController extends Controller
{
    public function home()
    {
        $featuredVehicles = Vehicle::with(['category', 'photos'])
            ->where('status', 'disponivel')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        return view('public.home', compact('featuredVehicles'));
    }

    public function vehicles(Request $request)
    {
        $query = Vehicle::with(['category', 'photos'])
            ->where('status', 'disponivel');

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('vehicle_category_id', $request->category_id);
        }

        $vehicles = $query->orderBy('id', 'desc')->paginate(12);
        $categories = VehicleCategory::orderBy('name')->get();

        return view('public.vehicles.index', compact('vehicles', 'categories'));
    }

    public function vehicleDetails($id)
    {
        $vehicle = Vehicle::with(['category', 'photos', 'accessories'])->findOrFail($id);
        
        // Similar vehicles from the same category
        $relatedVehicles = Vehicle::with(['category', 'photos'])
            ->where('status', 'disponivel')
            ->where('vehicle_category_id', $vehicle->vehicle_category_id)
            ->where('id', '!=', $vehicle->id)
            ->take(3)
            ->get();

        return view('public.vehicles.show', compact('vehicle', 'relatedVehicles'));
    }
}
