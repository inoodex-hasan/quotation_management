@extends('frontend.layouts.app')

@section('content')
    <div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="content-page-header">
                <h5>Permission List</h5>
            </div>
        </div>
        <!-- /Page Header -->

        <div class="row">
            <div class="col">
                <div class="card">

                    <div class="card-header">
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('permission.create') }}" class="btn btn-info">Create Permissions</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>SL</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                        <th>SL</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($permissions->chunk(2) as $chunk)
                                        <tr>
                                            @foreach ($chunk as $item)
                                                <td>{{ $loop->parent->index * 2 + $loop->index + 1 }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light border" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item"
                                                                    href="{{ route('permission.edit', $item->id) }}">Edit</a>
                                                            </li>
                                                            <li>
                                                                <button type="button" class="dropdown-item text-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#myModal{{ $item->id }}">
                                                                    Delete
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            @endforeach

                                            @if ($chunk->count() < 2)
                                                <!-- Fill empty cells if chunk has only 1 item -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modals -->
        @foreach ($permissions as $item)
            <div id="myModal{{ $item->id }}" class="modal fade" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Permission</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this permission:
                            <strong style="color: darkorange">{{ $item->name }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <form action="{{ route('permission.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('delete')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
@endsection
