<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = MenuItem::query();
            
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            $menuItems = $query->with('category')
                ->where('is_available', true)
                ->get()
                ->map(function ($item) {
                    // Ensure price is properly formatted
                    $item->price = number_format((float)$item->price, 2, '.', '');
                    return $item;
                });

            Log::info('Menu items fetched', ['count' => $menuItems->count(), 'items' => $menuItems->toArray()]);

            return response()->json([
                'status' => 'success',
                'data' => $menuItems
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch menu items', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch menu items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menuItem)
    {
        return response()->json([
            'status' => 'success',
            'data' => $menuItem->load('category')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'nullable|exists:categories,id',
                'is_available' => 'boolean',
                'image_url' => 'nullable|string'
            ]);

            $menuItem = MenuItem::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Menu item created successfully',
                'data' => $menuItem
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to create menu item', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create menu item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MenuItem $menuItem)
    {
        try {
            $validated = $request->validate([
                'name' => 'string|max:255',
                'description' => 'string',
                'price' => 'numeric|min:0',
                'category_id' => 'exists:categories,id',
                'is_available' => 'boolean',
                'image_url' => 'nullable|string'
            ]);

            $menuItem->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Menu item updated successfully',
                'data' => $menuItem
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update menu item', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update menu item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem)
    {
        try {
            $menuItem->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Menu item deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete menu item', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete menu item',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
