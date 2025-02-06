<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            foreach ($request->items as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);
                $totalAmount += $menuItem->price * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'status' => 'pending'
            ]);

            foreach ($request->items as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $menuItem->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $menuItem->price,
                    'subtotal' => $menuItem->price * $item['quantity']
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order->load('orderItems.menuItem')
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            // For managers/admins, show all orders
            if (in_array($request->user()->role, ['manager', 'admin'])) {
                $orders = Order::with(['orderItems.menuItem', 'user'])
                              ->orderBy('created_at', 'desc')
                              ->get();

                return response()->json([
                    'status' => 'success',
                    'data' => $orders
                ]);
            }

            // For regular users, show only their orders
            $orders = Order::with(['orderItems.menuItem'])
                          ->where('user_id', Auth::id())
                          ->orderBy('created_at', 'desc')
                          ->get();

            return response()->json([
                'status' => 'success',
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load orders'
            ], 500);
        }
    }

    public function show(Order $order)
    {
        // Only managers can view any order
        if (Auth::user()->role !== 'manager' && $order->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($order->load('orderItems.menuItem'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        try {
            Log::info('Updating order status', [
                'order_id' => $order->id,
                'current_status' => $order->status,
                'new_status' => $request->status
            ]);

            // Validate the request
            $validated = $request->validate([
                'status' => ['required', 'string', Rule::in(Order::getValidStatuses())]
            ]);

            DB::beginTransaction();

            // Update status
            $order->status = $validated['status'];
            $saved = $order->save();

            Log::info('Order save result', [
                'saved' => $saved,
                'order_status' => $order->status
            ]);

            if (!$saved) {
                throw new \Exception('Failed to save order status');
            }

            // Get fresh data with relationships
            $order->refresh();
            $order->load(['orderItems.menuItem', 'user']);

            DB::commit();

            Log::info('Order status updated successfully', [
                'order_id' => $order->id,
                'new_status' => $order->status
            ]);

            // Return clean JSON response
            return response()
                ->json([
                    'status' => 'success',
                    'message' => 'Order status updated successfully',
                    'data' => $order
                ])
                ->header('Content-Type', 'application/json; charset=utf-8')
                ->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error', ['errors' => $e->errors()]);
            return response()
                ->json([
                    'status' => 'error',
                    'message' => $e->errors()['status'][0] ?? 'Invalid status'
                ], 422)
                ->header('Content-Type', 'application/json; charset=utf-8');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order status update failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return response()
                ->json([
                    'status' => 'error',
                    'message' => 'Failed to update order status: ' . $e->getMessage()
                ], 500)
                ->header('Content-Type', 'application/json; charset=utf-8');
        }
    }
}
