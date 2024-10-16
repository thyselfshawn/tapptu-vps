@extends('layouts.app')
@push('styles')
    <style>
        @media (max-width: 600px) {
            .img-fluid {
                max-width: 75px;
                width: 75px;
                height: 75px;
            }
        }
    </style>
@endpush
@section('content')
    <div class="container">
        @if (empty($venue->currentSubscription()->xendit_plan_id))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                This venue is using trial
                {{ strtoupper($venue->currentSubscription()->package->name) }}
                ({{ strtolower($venue->currentSubscription()->package->type) }}ly)
                subscription plan.
                Ending at
                {{ $venue->currentSubscription()->end_at->format('F j, Y') }}.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if ($venue->currentSubscription() && $venue->currentSubscription()->isEndingSoon(7))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ $venue->name }}'s subscription ends in
                {{ $venue->currentSubscription()->timeUntilEnds() }}.
                Please renew soon!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    Venue Detail
                    <a href="{{ route('venues.index') }}" class="btn btn-secondary">Back to List</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex">
                            <div>
                                <h5 class="card-title">{{ $venue->name }}</h5>
                                @if (auth()->user()->role == 'admin')
                                    <p class="card-text">
                                        <strong>User:</strong>
                                        <a href="{{ route('users.show', $venue->user) }}">{{ $venue->user->email }}</a>
                                    </p>
                                    <p class="card-text">
                                        <strong>Google Place URL:</strong>
                                        <a class="text-primary"
                                            href="https://www.google.com/maps/place/?q=place_id:{{ $venue->googleplaceid }}"
                                            target="_blank">
                                            <i class="bi bi-link"></i>
                                        </a>
                                    </p>
                                    <p class="card-text"><strong>Google Review Start:</strong>
                                        {{ ucfirst($venue->googlereviewstart) }}</p>
                                    <p class="card-text"><strong>Status:</strong> {{ ucfirst($venue->status) }}</p>
                                @endif
                            </div>
                            @if ($venue->logo)
                                <img src="{{ route('venues.image', ['filename' => $venue->logo]) }}" alt="Venue Logo"
                                    class="img-fluid rounded-circle" style="max-width: 150px;" loading="lazy">
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-6 mt-2">
                        <div class="card">
                            <h5 class="card-header mb-2">RFID Cards</h5>
                            <table class="card-body table">
                                <thead>
                                    <tr>
                                        <th>UUID</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($venue->cards as $rfid)
                                        <tr>
                                            <td>{{ $rfid->uuid }}</td>
                                            <td>{{ $rfid->status }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No records found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Subscription Section -->
                    <div class="col-sm-12 col-md-6 col-lg-6 mt-2">
                        <div class="card">
                            <h5 class="card-header">Subscription Details</h5>
                            <div class="card-body">
                                @if ($venue->currentSubscription())
                                    <p>
                                        <strong>Current Package:</strong>
                                        {{ $venue->currentSubscription()->package->name }}
                                        {{ $venue->currentSubscription()->package->type }}
                                    </p>
                                    <p><strong>Next Billing Date:</strong>
                                        {{ $venue->currentSubscription()->end_at->format('F j, Y') }}</p>
                                @else
                                    <p>No active membership.</p>
                                @endif

                                <!-- Form for updating subscription -->
                                <form action="{{ route('billing.createSubscription') }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <input type="text" name="venue" value="{{ $venue->slug }}" hidden>
                                        <label for="package">Select Package</label>
                                        <select name="package" id="package" class="form-control">
                                            @foreach ($packages as $package)
                                                @if (!$venue->currentSubscription() || $package->id !== $venue->currentSubscription()->package_id)
                                                    <option value="{{ $package->id }}">{{ $package->name }}
                                                        {{ $package->type }}ly</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Subscription</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="mt-3">
                    @if (auth()->user()->role == 'admin' || auth()->user()->id == $venue->user_id)
                        <a href="{{ route('venues.edit', $venue->slug) }}" class="btn btn-warning">Edit Venue</a>
                    @endif
                    @if (auth()->user()->role == 'admin')
                        <form action="{{ route('venues.destroy', $venue->slug) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete
                                Venue</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
