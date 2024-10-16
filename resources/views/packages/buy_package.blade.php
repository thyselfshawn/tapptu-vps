@extends('layouts.app')

@push('styles')
    <!-- Add custom styles here -->
@endpush

@section('content')
    <div class="row">
        @foreach ($packages as $package)
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ ucfirst($package->name) }} Package</h5>
                        <p class="card-text">{{ ucfirst($package->type) }} Plan</p>
                        <p class="card-text">First Price: ${{ $package->first_price }}</p>
                        <p class="card-text">Second Price: ${{ $package->second_price }}</p>
                        <a href="#" class="btn btn-primary">Buy Now</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
    <!-- Add custom scripts here -->
@endpush
