<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private function hasProductsTable(): bool
    {
        return Schema::hasTable('products');
    }

    private function missingProductsTableMessage(): string
    {
        return "The 'products' table is missing. Import DB or run migrations.";
    }

    // Show all products
   public function index(Request $request)
{
    if (!$this->hasProductsTable()) {
        $products = new LengthAwarePaginator(
            [],
            0,
            10,
            1,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('frontend.pages.products.index', compact('products'))
            ->with('warning', $this->missingProductsTableMessage());
    }

    // Start query
    $query = Product::query();

    // Apply filters if present
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('product_code')) {
        $query->where('product_code', 'like', '%' . $request->product_code . '%');
    }

    // Paginate
    $products = $query->latest()->paginate(10)->withQueryString();

    return view('frontend.pages.products.index', compact('products'));
}

    // Show create form
    public function create()
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        return view('frontend.pages.products.create');
    }

    // Store new product
    public function store(Request $request)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => 'required|string|max:255|unique:products',
            'details' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            Product::create($request->all());
        } catch (QueryException $e) {
            return back()->withInput()->with('warning', 'Unable to save product right now.');
        }

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    // Show single product
    public function show($id)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $product = Product::find($id);
        if (!$product) {
            return redirect()->route('products.index')->with('warning', 'Product not found.');
        }

        return view('frontend.pages.products.show', compact('product'));
    }

    // Show edit form
    public function edit($id)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $product = Product::find($id);
        if (!$product) {
            return redirect()->route('products.index')->with('warning', 'Product not found.');
        }

        return view('frontend.pages.products.edit', compact('product'));
    }

    // Update product
    public function update(Request $request, $id)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $product = Product::find($id);
        if (!$product) {
            return redirect()->route('products.index')->with('warning', 'Product not found.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'product_code' => ['required', 'string', 'max:255', Rule::unique('products', 'product_code')->ignore($product->id)],
            'details' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
        ]);

        try {
            $product->update($request->all());
        } catch (QueryException $e) {
            return back()->withInput()->with('warning', 'Unable to update product right now.');
        }

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    // Delete product
    public function destroy($id)
    {
        if (!$this->hasProductsTable()) {
            return redirect()->route('products.index')
                ->with('warning', $this->missingProductsTableMessage());
        }

        $product = Product::find($id);
        if (!$product) {
            return redirect()->route('products.index')->with('warning', 'Product not found.');
        }

        try {
            $product->delete();
        } catch (QueryException $e) {
            return redirect()->route('products.index')->with('warning', 'Unable to delete product right now.');
        }

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
