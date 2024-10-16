@extends('layouts.app')

@push('styles')
    <!-- Add custom styles here -->
@endpush

@section('content')
    <div class="container">
        <h1>Create Package</h1>

        <form action="{{ route('packages.store') }}" method="POST">
            @csrf

            <div class="row mb-3">
                <div class="col-12">
                    <label for="name" class="form-label">Name</label>
                    <select name="name" class="form-control" required>
                        <option value="standard">Standard</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="type" class="form-label">Type</label>
                    <select name="type" class="form-control" required>
                        <option value="month">Monthly</option>
                        <option value="year">Yearly</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="first_price" class="form-label">First Price</label>
                    <input type="number" name="first_price" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="second_price" class="form-label">Second Price</label>
                    <input type="number" name="second_price" class="form-control" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Create Package</button>
        </form>
    </div>
@endsection

@push('scripts')
    <!-- Add custom scripts here -->
@endpush
