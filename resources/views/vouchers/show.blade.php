@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Voucher Details</h1>
        <p><strong>UUID:</strong> {{ $voucher->uuid }}</p>
        <p><strong>Venue:</strong> {{ $voucher->venue->name }}</p>
        <p><strong>Amount:</strong> {{ $voucher->amount }}</p>
        <p><strong>Status:</strong> {{ $voucher->status }}</p>
        <a href="{{ route('vouchers.index') }}" class="btn btn-primary">Back to Vouchers</a>
    </div>
@endsection
