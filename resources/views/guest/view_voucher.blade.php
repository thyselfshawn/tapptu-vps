@extends('guest.layout')

@section('content')
    <h6 class="offcanvas-title mb-3">Click to claim voucher!</h6>
    {{ auth()->user()->id }}
    <a class="btn btn-warning btn-lg"
        href="{{ route('guest.claim_voucher', ['venue' => $venue->slug, 'uuid' => $voucher->uuid]) }}"
        onclick="event.preventDefault();document.getElementById('logout-form').submit();">
        {{ __('Claim Now!') }}
    </a>
    <form id="logout-form" action="{{ route('guest.claim_voucher', ['venue' => $venue->slug, 'uuid' => $voucher->uuid]) }}" method="POST" class="d-none">
        @csrf
    </form>
@endsection