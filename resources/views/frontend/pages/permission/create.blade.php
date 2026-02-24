@extends('frontend.layouts.app')

@section('content')
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="content-page-header">
                <h5>Create Permission</h5>
            </div>
        </div>
        <!-- /Page Header -->

        @if ($errors->any())
            <div class="alert alert-danger" id="validation-error-alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>

            <script>
                // Set a timeout to hide the alert after 2000 milliseconds (2 seconds)
                setTimeout(function () {
                    document.getElementById('validation-error-alert').style.display = 'none';
                }, 3000);
            </script>
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1"></h4>
                        <div class="flex-shrink-0">
                            <div class="form-check form-switch form-switch-right form-switch-md">
                                <a href="{{ route('permission.index') }}" class="btn btn-info">Permission List</a>
                            </div>
                        </div>
                    </div>
                    <!-- end card header -->
                    <div class="card-body">
                        <div class="live-preview">
                            <div class="row gy-4">
                                <form action="{{ route('permission.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-xxl-3 col-md-6 mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" value="{{ old('name') }}" id="name"
                                                name="name" placeholder="Enter Permission name">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary float-end">Submit</button>
                                </form>

                            </div>
                            <!--end row-->
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!-- container-fluid -->
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