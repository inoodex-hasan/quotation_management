@extends('frontend.layouts.app')

@section('content')
    <div class="container-fluid mt-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold">Product List</h3>
            <a href="{{ route('products.create') }}" class="btn btn-primary rounded-pill px-4">
                + Add Product
            </a>
        </div>

        {{-- Filter Form --}}
        <div class="card mb-3 shadow-sm border-0 rounded-3">
            <div class="card-body">
                <form method="GET" action="{{ route('products.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" value="{{ request('name') }}" class="form-control"
                            placeholder="Search by Name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Product Code</label>
                        <input type="text" name="product_code" value="{{ request('product_code') }}" class="form-control"
                            placeholder="Search by Product Code">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body">

                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Product Code</th>
                            <th>Unit</th>
                            <th>Price</th>
                            <th class="text-end" width="90">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $key => $product)
                            <tr>
                                <td>{{ $products->firstItem() + $key }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ ucfirst($product->unit) }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('products.edit', $product->id) }}">Edit</a>
                                            </li>
                                            <li>
                                                <form action="{{ route('products.destroy', $product->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button onclick="return confirm('Delete this product?')" type="submit"
                                                        class="dropdown-item text-danger">Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No products found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $products->links() }}
                </div>

            </div>
        </div>

    </div>
@endsection
