@extends('layouts.app')

@section('content')
    <!-- Memberships Table -->
    <div class="card">
        <div class="card-header h4">Subscriptions</div>
        <div class="card-body">
            {{ $dataTable->table() }}
        </div>
    </div>
@endsection
@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
