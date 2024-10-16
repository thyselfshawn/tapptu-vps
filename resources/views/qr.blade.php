@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                @auth
                <div class="card-header">{{ __('Venue') }}</div>
                <div class="card-body">
                    @php
                        $venues = auth()->user()->venues;
                    @endphp
                    <div class="mb-3">
                        <label for="venue" class="form-label">Add to existing Venue</label>
                        <select class="form-select @error('venue') is-invalid @enderror" id="venue" name="venue">
                            @foreach($venues as $item)
                                <option value="{{ $item->id . '/' . session('setup-card') }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        @error('venue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <a
                            class="btn btn-secondary"
                            href="{{ route('venues.create') }}">Create new Venue
                        </a>
                    </div>
                </div>
                @else
                    <div class="card-body">
                        <div class="d-flex justify-content-center gap-3">
                            <a class="btn btn-info" href="{{ route('login') }}">Login</a>
                            <a class="btn btn-success" href="{{ route('register') }}">Register</a>
                        </div>
                    </div>
                @endauth                
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @if(auth()->user())
        <script>
            document.getElementById('venue').addEventListener('change', function() {
                var venueId = this.value;
                if (venueId) {
                    var url = '/attach-card/' + venueId;
                    window.location.href = url;
                }
            });
        </script>
    @endif
@endpush