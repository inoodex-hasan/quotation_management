@extends('frontend.layouts.app')

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="content-page-header">
                <h2> Projects List</h2>
                <a href="{{ route('projects.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Project
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-fluid">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Client/Company Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Budget</th>
                                    <th>Status</th>
                                    {{-- <th>Profit</th> --}}
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($projects as $project)
                                    <tr>
                                        <td>{{ $project->project_name }}</td>
                                        <td>{{ $project->client->name }}</td>
                                        <td>{{ $project->client->email }}</td>
                                        <td>{{ $project->client->phone }}</td>

                                        <td><strong>{{ number_format($project->budget, 2) }} </strong></td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $project->status == 'completed' ? 'success' : ($project->status == 'in_progress' ? 'warning' : ($project->status == 'cancelled' ? 'danger' : 'secondary')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                            </span>
                                        </td>
                                        {{-- <td>
                                            <span
                                                class="badge bg-{{ $project->profit_margin >= 0 ? 'success' : 'danger' }}">
                                                {{ number_format($project->total_profit, 2) }}
                                            </span>
                                        </td> --}}
                                        <td class="d-flex align-items-center">
                                            <div class="dropdown dropdown-action">
                                                <a href="#" class="btn-action-icon" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <ul>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('projects.show', $project->id) }}">
                                                                <i class="far fa-eye me-2"></i>View
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('projects.edit', $project->id) }}">
                                                                <i class="far fa-edit me-2"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a onclick="if (confirm('Are you sure to delete?')) { document.getElementById('serviceDelete{{ $project->id }}').submit(); }"
                                                                class="dropdown-item" href="javascript:void(0)">
                                                                <i class="far fa-trash-alt me-2"></i>Delete
                                                            </a>
                                                            <form id="serviceDelete{{ $project->id }}"
                                                                action="{{ route('projects.destroy', $project->id) }}"
                                                                method="POST" style="display:none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($projects->isEmpty())
                        <div class="text-center py-4">
                            <h5>No projects found</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
