<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function items(Request $request): JsonResponse
    {
        $items = InventoryItem::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $items,
        ]);
    }

    public function storeItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:50|unique:inventory_items,sku',
            'name' => 'required|string|max:150',
            'category' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'unit_cost' => 'nullable|numeric|min:0',
        ]);

        $item = InventoryItem::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Material gudang berhasil ditambahkan.',
            'data' => $item,
        ], 201);
    }

    public function adjustStock(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'adjustment' => 'required|integer',
        ]);

        $item = InventoryItem::findOrFail($id);
        $item->stock += (int) $request->adjustment;
        $item->status = $item->stock <= 0 ? 'HABIS' : ($item->stock <= $item->min_stock ? 'MENIPIS' : 'AMAN');
        $item->save();

        return response()->json([
            'status' => 'success',
            'message' => "Stok material {$item->name} berhasil diperbarui menjadi {$item->stock} {$item->unit}.",
            'data' => $item,
        ]);
    }

    public function leads(): JsonResponse
    {
        $leads = Lead::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $leads,
        ]);
    }

    public function branches(): JsonResponse
    {
        $branches = Branch::all();
        return response()->json([
            'status' => 'success',
            'data' => $branches,
        ]);
    }
}
