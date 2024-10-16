@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit RFID</h2>
    <form action="{{ route('cards.update', $card->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $card->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="token" class="form-label">RFID Token</label>
            <input type="text" class="form-control @error('token') is-invalid @enderror" id="token" name="token" value="{{ old('token', $card->token) }}" required>
            @error('token')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                <option value="pending" {{ old('status', $card->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="setup" {{ old('status', $card->status) == 'setup' ? 'selected' : '' }}>Setup</option>
                <option value="trial" {{ old('status', $card->status) == 'trial' ? 'selected' : '' }}>Trial</option>
                <option value="paid" {{ old('status', $card->status) == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
            @error('status')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <button type="submit" class="btn btn-primary">Update RFID</button>
        <a href="{{ route('cards.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
