@extends('layouts.app')

@push('styles')
    <!-- Add custom styles here -->
@endpush

@section('content')
    <div class="card">
        <div class="card-header h4">Packages</div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Actions</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>First Price</th>
                        <th>Second Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packages as $package)
                        <tr>
                            <td>
                                @if (auth()->user()->role == 'admin')
                                    <a href="{{ route('packages.edit', $package->id) }}" class="btn btn-warning"><i
                                            class="bi bi-pencil-square"></i></a>
                                    <form action="{{ route('packages.destroy', $package->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                @else
                                    <a href="{{ route('packages.subscribe', $package->id) }}"
                                        class="btn btn-warning">Subscribe</a>
                                @endif
                            </td>
                            <td>{{ ucfirst($package->name) }}</td>
                            <td>{{ ucfirst($package->type) }}</td>
                            <td>{{ $package->first_price }}</td>
                            <td>{{ $package->second_price }}</td>
                            <td>{{ $package->status ? 'Active' : 'Inactive' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Add custom scripts here -->
@endpush
