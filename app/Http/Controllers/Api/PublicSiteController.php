<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Vehicle;
use App\Models\VehicleCategory;
use Illuminate\Http\Request;

class PublicSiteController extends Controller
{
    /**
     * Get available vehicles list for public website.
     */
    public function getVehicles(Request $request)
    {
        $tenantId = $request->header('X-Tenant-ID') ?? 1; // Basic multitenant/branch support if needed

        $vehicles = Vehicle::with(['category', 'photos', 'brand', 'model']) // Assuming brand/model might be relations or attributes
            ->where('status', 'disponivel')
            ->orderBy('id', 'desc')
            ->paginate(12);

        return response()->json($vehicles);
    }

    /**
     * Get specific vehicle details.
     */
    public function getVehicleDetails($id)
    {
        $vehicle = Vehicle::with(['category', 'photos', 'accessories'])->findOrFail($id);

        return response()->json($vehicle);
    }

    /**
     * Get vehicle categories for filtering.
     */
    public function getCategories()
    {
        $categories = VehicleCategory::orderBy('name')->get();

        return response()->json($categories);
    }

    /**
     * Get available branches for pickup/return.
     */
    public function getBranches()
    {
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return response()->json($branches);
    }
}
