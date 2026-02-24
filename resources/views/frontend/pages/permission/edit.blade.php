@extends('frontend.layouts.app')

@section('content')
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="content-page-header">
                <h5>Edit Permission</h5>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <div
                            class="form-check form-switch form-switch-right form-switch-md d-flex justify-content-end text-white">
                            <a href="{{ route('permission.index') }}" class="btn btn-info">Permission List</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('permission.update', $permission->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="input-block mb-3 row">
                                <label class="col-form-label col-md-2">Text Input</label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control" value="{{ old('name', $permission->name) }}"
                                        id="name" name="name" placeholder="Enter Permission name">
                                </div>
                            </div>
                            <div class="input-block mb-3 mb-0 row">
                                <div class="col-md-10">
                                    <div class="input-group mb-3">
                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @section('script')
        <script>
            ClassicEditor
                .create(document.querySelector('#editor'))
                .catch(error => {
                    console.error(error);
                });
        </script>
    @endsection
@endsection