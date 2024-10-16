@extends('layouts.app')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">User Profile</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Avatar -->
                <div class="col-md-4 text-center">
                    @if ($user->avatar)
                        <img src="{{ route('users.image', ['filename' => $user->avatar]) }}" alt="User Avatar"
                            class="img-fluid rounded-circle" style="max-width: 150px;" loading="lazy">
                    @endif
                </div>

                <!-- User Info -->
                <div class="col-md-8">
                    <h4 class="card-title">{{ $user->name }}</h4>
                    <p class="card-text"><strong>Email:</strong> {{ $user->email }}</p>
                    <p class="card-text"><strong>Joined:</strong> {{ $user->created_at->format('Y-M-d') }}</p>
                    @if ($user->role == 'admin')
                        <p class="card-text"><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
@endsection


@push('scripts')
@endpush
