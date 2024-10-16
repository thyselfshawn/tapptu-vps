@extends('layouts.app')

@section('content')
    @if (auth()->user()->role == 'admin')
        <div class="card mb-3">
            <div class="card-header h4">Cards</div>
            <div class="card-body">
                <div class="row h5">
                    <div class="col-lg-6 col-12">
                        <!-- generate Form -->
                        <form action="{{ route('cards.store') }}" method="POST" class="mb-4">
                            @csrf
                            <div class="row mb-3">
                                <div class="form-group col-9 mb-3">
                                    <label for="rfid" class="form-label">Generate Card</label>
                                    <input type="number" name="number" id="rfid" class="form-control"
                                        placeholder="Enter number of RFID to generate"
                                        value="{{ request('searchNumber') }}">
                                </div>
                                <div class="col-4 h5">
                                    <button type="submit" class="btn btn-info w-100">Create</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-6 col-12">
                        <!-- Download Form -->
                        <form action="{{ route('cards.download') }}" method="POST" class="mb-4 download">
                            @csrf
                            <div class="row mb-3 h5">
                                <div class="form-group col-md-6">
                                    <label for="rfid" class="form-label">Card From</label>
                                    <input type="number" name="cardfrom" class="form-control" placeholder="From">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="rfid" class="form-label">Card To</label>
                                    <input type="number" name="cardto" class="form-control" placeholder="To">
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" id="download-qr" class="btn btn-warning w-100">Download Qr</button>
                                <button type="submit" id="download-csv" class="btn btn-info w-100">Download CSV</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- RFIDs Table -->
    <div class="card mb-3">
        @if (auth()->user()->role != 'admin')
            <div class="card-header h4">Cards</div>
        @endif
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table(['class' => 'table table-striped table-bordered']) !!}
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    <script>
        document.getElementById('download-qr').addEventListener('click', function() {
            appendInput('qr');
        });

        document.getElementById('download-csv').addEventListener('click', function() {
            appendInput('csv');
        });

        function appendInput(type) {
            let form = document.querySelector('.download');
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'type';
            input.value = type;
            form.appendChild(input);
        }
    </script>
@endpush
