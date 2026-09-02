<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Package;
use App\Models\Promo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(): JsonResponse
    {
        $packages = Package::where('is_active', true)->withCount('customers')->get();
        return response()->json([
            'status' => 'success',
            'data' => $packages,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'speed_mbps' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'default_ppn_mode' => 'nullable|in:include,exclude',
            'category' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $package = Package::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Paket internet baru berhasil ditambahkan.',
            'data' => $package,
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $package = Package::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'speed_mbps' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:0',
            'default_ppn_mode' => 'nullable|in:include,exclude',
            'category' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        $package->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Paket berhasil diperbarui.',
            'data' => $package,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $package = Package::findOrFail($id);
        $package->update(['is_active' => false]);

        return response()->json([
            'status' => 'success',
            'message' => 'Paket telah dinonaktifkan.',
        ]);
    }

    public function addons(): JsonResponse
    {
        $addons = Addon::where('is_active', true)->get();
        return response()->json([
            'status' => 'success',
            'data' => $addons,
        ]);
    }

    public function promos(): JsonResponse
    {
        $promos = Promo::where('status', 'AKTIF')->get();
        return response()->json([
            'status' => 'success',
            'data' => $promos,
        ]);
    }
}
