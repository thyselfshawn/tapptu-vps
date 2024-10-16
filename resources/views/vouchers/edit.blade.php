@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Voucher</h1>
        <form action="{{ route('vouchers.update', $voucher) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="uuid">UUID</label>
                <input type="text" name="uuid" class="form-control" value="{{ $voucher->uuid }}" required>
            </div>
            <div class="form-group">
                <label for="venue_id">Venue</label>
                <select name="venue_id" class="form-control" required>
                    <!-- Populate options with venues -->
                </select>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="text" name="amount" class="form-control" value="{{ $voucher->amount }}" required>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <input type="text" name="status" class="form-control" value="{{ $voucher->status }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
