<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category', 'ingredients')->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.price' => 'required|numeric|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Calculate cost price from ingredients
        $costPrice = 0;
        foreach ($request->ingredients as $ingredient) {
            $costPrice += floatval($ingredient['price']);
        }
        
        // Create product
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'selling_price' => $request->selling_price,
            'cost_price' => $costPrice,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'image' => $imagePath,
        ]);
        
        // Add ingredients
        foreach ($request->ingredients as $ingredient) {
            $product->ingredients()->create([
                'name' => $ingredient['name'],
                'price' => $ingredient['price'],
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category', 'ingredients');
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('ingredients');
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'ingredients' => 'required|array|min:1',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.price' => 'required|numeric|min:0',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Calculate cost price from ingredients
        $costPrice = 0;
        foreach ($request->ingredients as $ingredient) {
            $costPrice += floatval($ingredient['price']);
        }
        
        // Update product
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'selling_price' => $request->selling_price,
            'cost_price' => $costPrice,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
            'image' => $imagePath,
        ]);
        
        // Delete old ingredients and add new ones
        $product->ingredients()->delete();
        foreach ($request->ingredients as $ingredient) {
            $product->ingredients()->create([
                'name' => $ingredient['name'],
                'price' => $ingredient['price'],
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
}
