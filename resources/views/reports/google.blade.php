@extends('layouts.app')

@section('content')
    <div class="card mb-2">
        <div class="card-header d-flex justify-content-between align-items-center">
            Google Records
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterReport"
                aria-controls="staticBackdrop">
                <i class="bi bi-sliders"></i>
            </button>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-12">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Staring Google Feedback</h5>
                                    <h2 class="card-text">{{ $statistics['startingFeedback'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Filtered Google Feedback</h5>
                                    <h2 class="card-text">{{ $statistics['googleFeedback'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Filter offcanvas -->
    <div class="offcanvas offcanvas-end filter-canvas" data-bs-backdrop="static" tabindex="-1" id="filterReport"
        aria-labelledby="staticBackdropLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="staticBackdropLabel">FILTER</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="flex-shrink-0 p-3" style="width: 280px;">

                <form method="get" action="{{ route('reports.google_filter') }}" class="row g-3 mb-4 mt-2">
                    <!-- Date Range Selection -->
                    @php
                        $range = request('range', '7'); // Default to '7' if 'range' is not in the request
                    @endphp

                    <!-- Date Range Selection -->
                    <div class="col-12 mb-3">
                        <label class="form-label">Date Range</label>
                        <div class="btn-group" role="group" aria-label="Date Range">
                            <input type="radio" class="btn-check" name="range" id="range1" value="1"
                                {{ $range == '1' ? 'checked' : '' }}>
                            <label class="btn btn-secondary" for="range1">Daily</label>

                            <input type="radio" class="btn-check" name="range" id="range7" value="7"
                                {{ $range == '7' ? 'checked' : '' }}>
                            <label class="btn btn-secondary" for="range7">Weekly</label>

                            <input type="radio" class="btn-check" name="range" id="range30" value="30"
                                {{ $range == '30' ? 'checked' : '' }}>
                            <label class="btn btn-secondary" for="range30">Monthly</label>

                            <input type="radio" class="btn-check" name="range" id="rangeAll" value="all"
                                {{ $range == 'all' ? 'checked' : '' }}>
                            <label class="btn btn-secondary" for="rangeAll">All</label>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="col-12">
                        <label for="venue" class="form-label">Venue</label>
                        <select name="venue" id="venue" class="form-select">
                            <option value="all">All Venues</option>
                            @foreach ($venues as $item)
                                <option value="{{ $item->slug }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="card" class="form-label">RFID Cards</label>
                        <select name="card" id="card" class="form-select">
                            <option value="all">All Cards</option>
                            @foreach ($cards as $item)
                                <option value="{{ $item->uuid }}">{{ $item->uuid }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
@endpush
