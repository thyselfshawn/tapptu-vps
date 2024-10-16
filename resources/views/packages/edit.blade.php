@extends('layouts.app')

@push('styles')
    <!-- Add custom styles here -->
@endpush

@section('content')
    <div class="container">
        <h1>Edit Package</h1>

        <form action="{{ route('packages.update', $package->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-12">
                    <label for="name" class="form-label">Name</label>
                    <select name="name" class="form-control" required>
                        <option value="standard" {{ $package->name == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="premium" {{ $package->name == 'premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" class="form-control" required>
                        <option value="month" {{ $package->type == 'month' ? 'selected' : '' }}>Monthly</option>
                        <option value="year" {{ $package->type == 'year' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="first_price" class="form-label">First Price</label>
                    <input type="number" name="first_price" class="form-control" value="{{ $package->first_price }}"
                        required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="second_price" class="form-label">Second Price</label>
                    <input type="number" name="second_price" class="form-control" value="{{ $package->second_price }}"
                        required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ $package->status ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$package->status ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Package</button>
        </form>
    </div>
@endsection

@push('scripts')
    <!-- Add custom scripts here -->
@endpush
