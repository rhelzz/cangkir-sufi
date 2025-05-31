<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        $products = Product::where('stock', '>', 0)->get();
        
        return view('cashier.index', compact('categories', 'products'));
    }
    
    public function getProducts(Request $request)
    {
        $categoryId = $request->category_id;
        $query = $request->query;
        
        $products = Product::query()->where('stock', '>', 0);
        
        if ($categoryId) {
            $products->where('category_id', $categoryId);
        }
        
        if ($query) {
            $products->where('name', 'like', "%{$query}%");
        }
        
        return response()->json([
            'products' => $products->with('category')->get()
        ]);
    }
    
    public function processOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,other',
        ]);
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'total_amount' => $request->total_amount,
                'tax' => $request->tax,
                'discount' => $request->discount,
                'final_amount' => $request->final_amount,
                'payment_method' => $request->payment_method,
                'user_id' => Auth::id(),
            ]);
            
            // Add order items
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Check stock
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Not enough stock for {$product->name}");
                }
                
                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->selling_price,
                    'subtotal' => $product->selling_price * $item['quantity'],
                ]);
                
                // Reduce stock
                $product->stock -= $item['quantity'];
                $product->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order processed successfully',
                'order' => $order->load('orderItems.product'),
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Order processing failed: ' . $e->getMessage(),
            ], 422);
        }
    }
    
    public function orderHistory()
    {
        $orders = Order::with(['user', 'orderItems.product'])
            ->latest()
            ->paginate(10);
            
        return view('cashier.history', compact('orders'));
    }
    
    public function viewOrder(Order $order)
    {
        $order->load(['user', 'orderItems.product']);
        return view('cashier.view-order', compact('order'));
    }
}
