<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, File, Storage};
use App\Models\{DailySale, Inventory, InventoryItem, Product, Sale, Service, SizeVsTopingPrice};
use App\Models\Admin\{Brand, Category, OptionTitle, ProductImage, ProductOption, ProductOptionTopping, ProductSize, ProductTag, ProductToping, Size, SubCategory, Toping};
use App\Http\Controllers\Controller;
use Validator;
use Carbon\Carbon;

class ProductContoller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product->latest()->get();
        // $brands = Brand::where('status', '1')->latest()->get();
        return view('frontend.pages.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('for_book_or_product', '2')->where('status', '1')->get();
        $subCategories = SubCategory::where('for_book_or_product', '2')->where('status', '1')->get();
        $tmp = [];
        foreach ($subCategories as $subCategory) {
            $tmp[$subCategory->category_id][] = $subCategory;
        }
        $subCategories = $tmp;
        $brands = Brand::where('status', '1')->get();

        return view('admin.pages.product.create', compact('categories', 'subCategories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'model_name' => 'required|string|max:255',
            'warranty' => 'required|integer',
            'status' => 'required|boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);

        $photoPaths = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {

                // Store temporarily in storage/app/public/products
                $tempPath = $photo->store('products', 'public');
                $filename = basename($tempPath);

                // Paths
                $from = storage_path('app/public/products/' . $filename);
                $to = public_path('uploads/products/' . $filename);

                // Ensure uploads directory exists
                if (!file_exists(public_path('uploads/products'))) {
                    mkdir(public_path('uploads/products'), 0777, true);
                }

                // Move file from storage to public/uploads
                rename($from, $to);

                // Save path for DB
                $photoPaths[] = 'uploads/products/' . $filename;
            }
        }

        $product = Product::create([
            'brand_id' => $validated['brand_id'],
            'name' => $validated['name'],
            'model' => $validated['model_name'],
            'warranty' => $validated['warranty'],
            'status' => $validated['status'],
            'photos' => !empty($photoPaths) ? $photoPaths : null,

        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::where('id', $id)->first();
        // $categories = Category::where('for_book_or_product', '2')->where('status', '1')->get();
        // $subCategories = SubCategory::where('for_book_or_product', '2')->where('status', '1')->get();
        $tmp = [];
        // foreach($subCategories as $subCategory){
        //     $tmp[$subCategory->category_id][] = $subCategory;
        // }
        // $subCategories = $tmp;

        $brands = Brand::where('status', '1')->get();

        if (!$product) {
            return redirect()->back()->with(['error' => getNotify(10)])->withInput();
        }

        return view('frontend.pages.product.edit', compact('product', 'id', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'name' => 'required|string|max:255',
            'model_name' => 'required|string|max:255',
            'warranty' => 'required|integer',
            'status' => 'required|boolean',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'existing_photos' => 'nullable|array',        // existing images kept
        ]);

        // Convert stored DB photos (could be array OR JSON)
        $oldPhotos = is_array($product->photos)
            ? $product->photos
            : json_decode($product->photos, true);

        if (!$oldPhotos) {
            $oldPhotos = [];
        }

        // Photos user wants to keep
        $existing = $request->has('existing_photos')
            ? $validated['existing_photos']
            : $oldPhotos;

        // Delete removed images from folder
        foreach ($oldPhotos as $photo) {
            if (!in_array($photo, $existing)) {
                $filePath = public_path($photo);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }

        // Start with existing photos
        $finalPhotos = $existing;

        // Handle new uploads
        if ($request->hasFile('photos')) {

            $uploadPath = public_path('uploads/products');
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            foreach ($request->file('photos') as $photo) {
                $filename = uniqid() . '.' . $photo->getClientOriginalExtension();
                $photo->move($uploadPath, $filename);
                $finalPhotos[] = "uploads/products/" . $filename;
            }
        }

        // Update product
        $product->update([
            'brand_id' => $validated['brand_id'],
            'name' => $validated['name'],
            'model' => $validated['model_name'],
            'warranty' => $validated['warranty'],
            'status' => $validated['status'],
            'photos' => $finalPhotos,

        ]);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }


    //     public function update(Request $request, $id)
// {
//     $product = Product::findOrFail($id);

    //     $validated = $request->validate([
//         'brand_id' => 'required|exists:brands,id',
//         'name' => 'required|string|max:255',
//         'model_name' => 'required|string|max:255',
//         'warranty' => 'required|integer',
//         'status' => 'required|boolean',
//         'photos' => 'nullable|array',
//         'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
//         'serial_number' => 'nullable|string|max:255',
//         'barcode_number' => 'nullable|string|max:255',
//         'remove_photos' => 'nullable|array'
//     ]);

    //     // Decode old photos
//     $existingPhotos = $product->photos ?? [];

    //     // Remove selected photos
//     if ($request->has('remove_photos')) {
//         foreach ($request->remove_photos as $removePhoto) {
//             // Delete file from server
//             if (file_exists(public_path($removePhoto))) {
//                 unlink(public_path($removePhoto));
//             }

    //             // Remove from existing photos array
//             $existingPhotos = array_values(array_filter($existingPhotos, function ($photo) use ($removePhoto) {
//                 return $photo !== $removePhoto;
//             }));
//         }
//     }

    //     // Upload new photos
//     if ($request->hasFile('photos')) {
//         foreach ($request->file('photos') as $photo) {

    //             // Temp store
//             $tempPath = $photo->store('products', 'public');
//             $filename = basename($tempPath);

    //             // Paths
//             $from = storage_path('app/public/products/' . $filename);
//             $to = public_path('uploads/products/' . $filename);

    //             if (!file_exists(public_path('uploads/products'))) {
//                 mkdir(public_path('uploads/products'), 0777, true);
//             }

    //             rename($from, $to);

    //             // Append new path
//             $existingPhotos[] = 'uploads/products/' . $filename;
//         }
//     }

    //     // Update product
//     $product->update([
//         'brand_id' => $validated['brand_id'],
//         'name' => $validated['name'],
//         'model' => $validated['model_name'],
//         'warranty' => $validated['warranty'],
//         'status' => $validated['status'],
//         'photos' => !empty($existingPhotos) ? json_encode($existingPhotos) : null,
//         'serial_number' => $validated['serial_number'],
//         'barcode_number' => $validated['barcode_number']
//     ]);

    //     return redirect()->route('products.index')->with('success', 'Product updated successfully.');
// }


    // public function update(Request $request, Product $product)
// {
//     $validated = $request->validate([
//         'brand_id' => 'required|exists:brands,id',
//         'name' => 'required|string|max:255',
//         'model_name' => 'required|string|max:255',
//         'warranty' => 'required|integer',
//         'status' => 'required|boolean',
//         'photos' => 'nullable|array',
//         'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
//         'remaining_photos' => 'nullable|string',
//         'serial_number' => 'nullable|string|max:255',
//         'barcode_number' => 'nullable|string|max:255'
//     ]);

    //     // Get remaining photos from hidden input
//     $remainingPhotos = [];
//     if ($request->remaining_photos) {
//         $remainingPhotos = json_decode($request->remaining_photos, true) ?? [];
//     }

    //     // Find deleted photos and remove them from storage
//     $originalPhotos = $product->photos ?? [];
//     $deletedPhotos = array_diff($originalPhotos, $remainingPhotos);

    //     foreach ($deletedPhotos as $deletedPhoto) {
//         if (Storage::disk('public')->exists($deletedPhoto)) {
//             Storage::disk('public')->delete($deletedPhoto);
//         }
//     }

    //     // Handle new photo uploads
//     $newPhotoPaths = [];
//     if ($request->hasFile('photos')) {
//         foreach ($request->file('photos') as $photo) {
//             $path = $photo->store('products', 'public');
//             $newPhotoPaths[] = $path;
//         }
//     }

    //     // Merge remaining and new photos
//     $allPhotos = array_merge($remainingPhotos, $newPhotoPaths);

    //     $product->update([
//         'brand_id' => $validated['brand_id'],
//         'name' => $validated['name'],
//         'model' => $validated['model_name'],
//         'warranty' => $validated['warranty'],
//         'status' => $validated['status'],
//         'photos' => !empty($allPhotos) ? $allPhotos : null,
//     ]);

    //     return redirect()->route('products.index')->with('success', 'Product updated successfully.');
// }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::where('id', $id)->first();
        if (!$product)
            abort(404);

        $product->delete();
        return redirect()->back()->with('success', 'Product delete successfully');
    }

    public function size($id)
    {
        $productSizes = ProductSize::join('sizes', 'sizes.id', '=', 'product_sizes.size_id')
            ->where('product_id', $id)
            ->select('product_sizes.*', 'sizes.name')->get();
        return view('admin.pages.product.product_size', compact('id', 'productSizes'));
    }

    public function getProductSize(Request $request)
    {

        $productSizes = ProductSize::join('sizes', 'sizes.id', '=', 'product_sizes.size_id')
            ->where('product_sizes.product_id', $request->id)
            ->where('product_sizes.status', '1')
            ->select('product_sizes.*', 'sizes.name')->get();
        $product = Product::where('id', $request->id)->first();

        return ['product' => $product, 'productSizes' => $productSizes];
    }

    public function createProductSize($id)
    {
        $product = Product::where('id', $id)->first();
        if (!$product) {
            return redirect()->back()->with(['error' => getNotify(10)])->withInput();
        }
        $sizes = Size::where('status', '1')->get();
        return view('admin.pages.product.create_product_size', compact('id', 'sizes', 'product'));
    }

    public function editProductSize($id)
    {
        $productSize = ProductSize::find($id);
        if (!$productSize) {
            return redirect()->back()->with(['error' => getNotify(10)]);
        }
        $product = Product::where('id', $productSize->product_id)->first();
        if (!$product) {
            return redirect()->back()->with(['error' => getNotify(10)]);
        }

        $sizes = Size::where('status', '1')->get();
        if ($productSize) {
            return view('admin.pages.product.edit_product_size', compact('productSize', 'sizes', 'product'));
        }
    }

    public function storeSize(Request $request)
    {
        $request->validate([
            'product_id' => 'required|numeric',
            'size_id' => 'required|numeric',
            'price' => 'nullable|numeric',
            'status' => 'required|in:0,1',
            // 'description' => 'required',
            'offer_price' => 'nullable|numeric',
            'offer_from' => 'nullable|date',
            'offer_to' => 'nullable|date',
            'quantity' => 'numeric|nullable'
        ]);

        $product = Product::where('id', $request->product_id)->first();
        if (!$product) {
            return redirect()->back()->with(['error' => getNotify(10)]);
        }

        if ($product->is_size_wise_price == '1' && $request->price == "") {
            return redirect()->back()->with(['error' => 'Price field is required.', 'error_code' => 'edit'])->withInput();
        }

        $imageName = "";
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $destinationPath = public_path('frontend/product_images/');
            $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $image->move($destinationPath, $imageName);
        }

        $size = new ProductSize;
        $size->product_id = $request->product_id;
        $size->size_id = $request->size_id;
        $size->price = $request->price ?? 0;
        $size->offer_price = $request->offer_price;
        $size->offer_from = $request->offer_from;
        $size->offer_to = $request->offer_to;
        $size->quantity = $request->quantity;
        $size->description = $request->description;
        $size->status = $request->status;
        $size->created_by = auth()->user()->id;
        $size->image = $imageName;
        $size->save();

        return redirect()->back()->with(['success' => getNotify(1)]);
    }
    //Assign topings
    public function topings($id)
    {
        $productTopings = ProductToping::join('topings', 'topings.id', '=', 'product_topings.toping_id')->where('product_topings.product_id', $id)->select('topings.*', 'product_topings.id as topId')->get();
        $topings = Toping::where('status', '1')->get();
        return view('admin.pages.product.topings', compact('productTopings', 'topings', 'id'));
    }

    public function storeToping(Request $request)
    {
        $request->validate([
            'product_id' => 'required|numeric',
            'toping' => 'required|numeric',
            'status' => 'required|in:0,1',
        ]);

        $checkExist = ProductToping::where('product_id', $request->product_id)->where('toping_id', $request->toping)->first();
        if (!$checkExist) {
            $size = new ProductToping();
            $size->product_id = $request->product_id;
            $size->toping_id = $request->toping;
            $size->status = $request->status;
            $size->created_by = auth()->user()->id;
            $size->save();
            session()->flash('sweet_alert', [
                'type' => 'success',
                'title' => 'Success!',
                'text' => 'Product toping added success',
            ]);
        } else {
            session()->flash('sweet_alert', [
                'type' => 'warning',
                'title' => 'warning!',
                'text' => 'Already exists this toping! Try another',
            ]);
        }


        return redirect()->back();
    }

    public function updateSize(Request $request, $id)
    {
        // return $request->all();
        $request->validate([
            'product_id' => 'required|numeric',
            'size_id' => 'required|numeric',
            'price' => 'nullable|numeric',
            'status' => 'required|in:0,1',
            'offer_price' => 'nullable|numeric',
            'offer_from' => 'nullable|date',
            'offer_to' => 'nullable|date',
            'quantity' => 'numeric|nullable'
        ]);

        $product = Product::where('id', $request->product_id)->first();
        if (!$product) {
            return redirect()->back()->with(['error' => getNotify(10)]);
        }

        if ($product->is_size_wise_price != '1' && $request->price == "") {
            return redirect()->back()->with(['error' => 'Price field is required.', 'error_code' => 'edit'])->withInput();
        }

        $size = ProductSize::find($id);
        if ($size) {

            $imageName = $size->image;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $destinationPath = public_path('frontend/product_images/');
                $imageName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $imageName);
                if ($size->image)
                    unlink(public_path('frontend/product_images/' . $size->image));
            }


            $size->size_id = $request->size_id;
            $size->price = $request->price ?? 0;
            $size->offer_price = $request->offer_price;
            $size->offer_from = $request->offer_from;
            $size->offer_to = $request->offer_to;
            $size->quantity = $request->quantity;
            $size->status = $request->status;
            $size->description = $request->description;
            $size->image = $imageName;
            $size->updated_by = auth()->user()->id;
            $size->update();

            return redirect()->back()->with(['success' => getNotify(2)]);
        }
    }

    public function deleteProductSize($id)
    {
        $productSizes = ProductSize::find($id);
        if ($productSizes)
            $productSizes->delete();
        session()->flash('sweet_alert', [
            'type' => 'success',
            'title' => 'Success!',
            'text' => 'Product Size delete success',
        ]);
        return redirect()->back();
    }

    public function getProducts()
    {
        $categories = Category::leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->select(
                'categories.id as category_id',
                'categories.order_by as OrderBY',
                'categories.name as category_name',
                'products.id as product_id',
                'products.name as product_name',
                'products.description as description',
                'products.image as image',
            )
            ->where('products.status', '1')
            ->orderBy('categories.order_by')
            ->orderBy('products.id')
            ->get();

        $currentDate = Carbon::today();
        foreach ($categories as $key => $category) {
            $productSizes = ProductSize::where('product_id', $category->product_id)->get();
            $offerMin = null;
            $regularMin = null;
            foreach ($productSizes as $size) {
                if ($size->offer_from <= $currentDate && $currentDate <= $size->offer_to) {
                    $offerPrice = $size->offer_price;
                    if ($offerMin == null)
                        $offerMin = $offerPrice;
                    $$offerMin = min($offerMin, $offerPrice);
                }
                $price = $size->price;
                if ($regularMin == null)
                    $regularMin = $price;
                $regularMin = min($regularMin, $price);
            }
            $categories[$key]->calculated_offer_price = ($offerMin < $regularMin ? $offerMin : null);
            $categories[$key]->min_price = $regularMin;
        }


        // return $categories;
        // Organize the result into a more usable format
        $groupedCategories = [];
        $categories = $categories->sortBy('order_by');
        foreach ($categories as $category) {
            // $category->min_price = null;
            // $category->calculated_offer_price = null;
            $categoryId = $category->category_id;
            if (!isset($groupedCategories[$categoryId])) {
                $groupedCategories[$categoryId] = [
                    'category_id' => $category->category_id,
                    'category_name' => $category->category_name,
                    'order_by' => $category->OrderBY,
                    'products' => [],
                ];
            }
            if ($category->product_id) {
                $groupedCategories[$categoryId]['products'][] = [
                    'id' => $category->product_id,
                    'name' => $category->product_name,
                    'description' => $category->description,
                    'image' => $category->image,
                    'min_price' => $category->min_price,
                    'calculated_offer_price' => $category->calculated_offer_price,
                ];
            }
        }
        $productAllTages = ProductTag::pluck('tag_name', 'id');
        return [$groupedCategories, $productAllTages];
    }

    public function getProductDetails(Request $request)
    {
        $productId = $request->query('id');
        $product = Product::where('id', $productId)->first();
        $productSizes = ProductSize::join('sizes', 'sizes.id', '=', 'product_sizes.size_id')
            ->where('product_id', $productId)
            ->where('product_sizes.status', '1')
            ->select('product_sizes.*', 'sizes.name', 'sizes.id as size_id')
            ->get();


        $currentDate = Carbon::today();
        $maxPrice = $productSizes->max('price');
        $minPrice = $productSizes->min('price');
        $tem = [];

        foreach ($productSizes as $row) {
            if ($row->offer_from <= $currentDate && $currentDate <= $row->offer_to) {
                $row->price = $row->offer_price;
            }
            $tem[$row->id] = $row;
        }
        $productSizes = $tem;
        $productTopings = ProductToping::join('topings', 'topings.id', '=', 'product_topings.toping_id')
            ->where('product_topings.product_id', $productId)
            ->where('product_topings.status', '1')
            ->select('topings.*')
            ->get();
        $favoritToppingsIds = [];
        foreach ($productTopings as $toping) {
            $favoritToppingsIds[$toping->id] = $toping->id;
        }


        $tem = [];
        foreach ($productTopings as $row) {
            $tem[$row->id] = $row;
        }
        $productTopings = $tem;

        $allTopings = Toping::where('status', '1')->get();

        $tem = [];
        foreach ($allTopings as $row) {
            $tem[$row->id] = $row;
        }
        $allTopings = $tem;

        $moreTopings = Toping::whereNotIn('id', $favoritToppingsIds)->where('status', '1')->get();

        $tem = [];
        foreach ($moreTopings as $row) {
            $tem[$row->id] = $row;
        }
        $moreTopings = $tem;

        $sizeVsTopings = SizeVsTopingPrice::get();
        $bindData = [];
        foreach ($sizeVsTopings as $item) {
            $bindData[$item->toping_id][$item->size_id] = $item->price;
        }
        $sizeVsTopings = $bindData;

        $maxMin = [$minPrice, $maxPrice];

        $productTages = ProductTag::where('pro_id', $productId)->get()->toArray();

        $options = ProductOption::join('product_option_toppings as option_topping', 'option_topping.product_option_id', '=', 'product_options.id')
            ->join('option_titles', 'option_titles.id', '=', 'product_options.title_id')
            ->where('product_options.product_id', $productId)
            ->select('option_topping.*', 'product_options.title_id', 'product_options.type', 'product_options.free_qty', 'option_titles.name')->get();

        $temp = [];
        foreach ($options as $option) {
            $option->type = strtolower($option->type);
            $temp[$option->product_option_id]['details']['title'] = $option->name;
            $temp[$option->product_option_id]['details']['freeQty'] = $option->free_qty;
            $temp[$option->product_option_id]['options'][] = $option;
        }
        $productOptions = $temp;


        return response()->json([$product, $productSizes, $productTopings, $maxMin, $allTopings, $moreTopings, $sizeVsTopings, $productTages, $productOptions]);
    }


    public function getPopularProducts()
    {
        return $topSellingProducts = \DB::table('products')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('product_sizes', function ($join) {
                $join->on('products.id', '=', 'product_sizes.product_id')
                    ->whereRaw('NOW() BETWEEN product_sizes.offer_from AND product_sizes.offer_to');
            })
            ->select(
                'products.id',
                'products.name',
                'products.image',
                \DB::raw('COUNT(orders.id) as total_orders'),
                \DB::raw('(SELECT MIN(price) FROM product_sizes WHERE product_sizes.product_id = products.id) as min_price'),
                'product_sizes.offer_price as calculated_offer_price'
            )
            ->groupBy('products.id', 'products.name', 'products.image', 'product_sizes.offer_price')
            ->orderBy('total_orders', 'desc')
            ->limit(10)
            ->get();
    }

    public function getRelatedProduct(Request $request)
    {
        $product_ids = $request->product_ids;
        $product_ids = explode(",", $product_ids);
        $catIds = Product::whereIn("id", $product_ids)->pluck('category_id');
        $products = Product::whereIn('category_id', $catIds)->where('status', '1')->take(10)->get();

        $proData = [];
        foreach ($products as $pro) {
            $proData[] = [
                'id' => $pro->id,
                'name' => $pro->name,
                'image' => asset("frontend/product_images/$pro->image"),
            ];
        }

        return $proData;
    }

    //     public function lookupBarcode(Request $request)
// {
//     $request->validate([
//         'barcode' => 'required|string'
//     ]);

    //     // Clean barcode
//     $barcode = preg_replace('/[^0-9]/', '', $request->barcode);

    //     // Check if product exists in database
//     $product = Product::where('barcode', $barcode)->first();

    //     if ($product) {
//         return response()->json([
//             'success' => true,
//             'product' => $product,
//             'message' => 'Product found in database'
//         ]);
//     }

    //     // Optional: Fetch from external API (Open Food Facts, UPCitemdb, etc.)
//     $externalData = $this->fetchExternalProductData($barcode);

    //     return response()->json([
//         'success' => true,
//         'external_data' => $externalData,
//         'message' => 'New barcode detected'
//     ]);
// }

    // private function fetchExternalProductData($barcode)
// {
//     try {
//         // Example using Open Food Facts API
//         $response = Http::get("https://world.openfoodfacts.org/api/v0/product/{$barcode}.json");

    //         if ($response->successful() && $response->json('status') == 1) {
//             $data = $response->json('product');
//             return [
//                 'name' => $data['product_name'] ?? null,
//                 'brand' => $data['brands'] ?? null,
//                 'model' => $data['model'] ?? null,
//                 'category' => $data['categories'] ?? null,
//                 'image' => $data['image_url'] ?? null,
//             ];
//         }
//     } catch (\Exception $e) {
//         \Log::error('External barcode API error: ' . $e->getMessage());
//     }

    //     return null;
// }

    // public function generateBarcode($barcode)
// {
//     // Using milon/barcode package
//     $generator = new \Milon\Barcode\DNS1D();
//     $barcodeImage = $generator->getBarcodeHTML($barcode, 'C128', 2, 30);

    //     return $barcodeImage;
// }

    public function scanAndAdd(Request $request)
    {
        $validated = $request->validate([
            'barcode_data' => 'required|string|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'product_name' => 'nullable|string|max:255',
            'model_name' => 'nullable|string|max:255',
        ]);

        $barcode = $validated['barcode_data'];

        // Check if barcode already exists in inventory_items
        $existingItem = InventoryItem::where('barcode_data', $barcode)->first();

        if ($existingItem) {
            // Barcode exists - just increase stock
            $product = $existingItem->product;

            // Update inventory stock
            $inventory = Inventory::where('product_id', $product->id)->first();
            if ($inventory) {
                $inventory->increment('current_stock');
            }

            // Create new inventory item with same product
            InventoryItem::create([
                'product_id' => $product->id,
                'barcode_data' => $barcode . '-' . time(), // Unique barcode
                'serial_number' => null,
                'unit_status' => 'available',
                'purchase_date' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Stock increased for existing product: ' . $product->name,
                'product' => $product,
                'action' => 'stock_increased'
            ]);
        }

        if (empty($validated['product_name'])) {
            $productName = "Product-" . substr($barcode, -6);
            $modelName = "MODEL-" . substr($barcode, -4);
        } else {
            $productName = $validated['product_name'];
            $modelName = $validated['model_name'] ?? "MODEL-" . substr($barcode, -4);
        }

        // Create new product
        $product = Product::create([
            'brand_id' => $validated['brand_id'] ?? 1, // Default brand
            'name' => $productName,
            'model' => $modelName,
            'warranty' => 0, // Default
            'status' => 1,
            'photos' => null,
        ]);

        // Create inventory with 1 stock
        $inventory = Inventory::create([
            'product_id' => $product->id,
            'opening_stock' => 1,
            'current_stock' => 1,
            'notes' => 'Added via barcode scan',
        ]);

        // Create inventory item with scanned barcode
        $inventoryItem = InventoryItem::create([
            'product_id' => $product->id,
            'serial_number' => null,
            'barcode_data' => $barcode,
            'unit_status' => 'available',
            'purchase_date' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'New product created and added to inventory',
            'product' => $product,
            'inventory' => $inventory,
            'action' => 'new_product'
        ]);
    }

    public function showScanPage()
    {
        $brands = Brand::all();
        return view('frontend.pages.product.scan-add', compact('brands'));
    }
}