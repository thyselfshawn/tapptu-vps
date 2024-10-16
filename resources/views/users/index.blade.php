@extends('layouts.app')

@section('content')
    <div class="card mb-3">
        <div class="card-header">Manage Users</div>
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table']) !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endpush
