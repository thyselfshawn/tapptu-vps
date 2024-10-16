@extends('guest.layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card">
                @auth
                    <div class="card-header">{{ __('Venue') }}</div>
                    <div class="card-body">
                        @if ($venues->count() > 0)
                            <div class="mb-3">
                                <label for="venue" class="form-label">Add to existing Venue</label>
                                <select class="form-select @error('venue') is-invalid @enderror" id="venue" name="venue">
                                    <option value="">All</option>
                                    @foreach ($venues as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                @error('venue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                        <div class="mb-3">
                            <a class="btn btn-secondary" href="{{ route('venues.create') }}?card={{ $card->uuid }}">Create new
                                Venue
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
@endsection

@push('scripts')
    @if (auth()->check())
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const venueSelect = document.getElementById('venue');
                if (venueSelect) {
                    venueSelect.addEventListener('change', function() {
                        const venueId = this.value;
                        if (venueId) {
                            const url =
                                "{{ route('attach_card', ['id' => ':venueId', 'card' => $card->uuid]) }}"
                                .replace(':venueId', venueId);
                            window.location.href = url;
                        }
                    });
                }
            });
        </script>
    @endif
@endpush
