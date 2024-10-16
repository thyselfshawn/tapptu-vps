@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Settings</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">WhatsApp Settings</h5>
                <p><strong>Number:</strong> {{ $setting->wa_number }}</p>
                <p><strong>Instance ID:</strong> {{ $setting->wa_instanceid }}</p>
                <p><strong>Access Token:</strong> <span id="wa_accesstoken">********</span> <button
                        class="btn btn-sm btn-secondary reveal-btn" data-field="wa_accesstoken">Reveal</button></p>

                <h5 class="card-title mt-4">Stripe Settings</h5>
                <p><strong>Publishable Key:</strong> {{ $setting->stripe_publishable }}</p>
                <p><strong>Secret Key:</strong> <span id="stripe_secret">********</span> <button
                        class="btn btn-sm btn-secondary reveal-btn" data-field="stripe_secret">Reveal</button></p>
                <p><strong>Webhook Secret:</strong> <span id="stripe_webhook_secret">********</span> <button
                        class="btn btn-sm btn-secondary reveal-btn" data-field="stripe_webhook_secret">Reveal</button></p>

                <a href="{{ route('settings.edit') }}" class="btn btn-primary">Edit Settings</a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const revealButtons = document.querySelectorAll('.reveal-btn');
                const sensitiveData = {
                    wa_accesstoken: '{{ $setting->wa_accesstoken }}',
                    stripe_secret: '{{ $setting->stripe_secret }}',
                    stripe_webhook_secret: '{{ $setting->stripe_webhook_secret }}'
                };

                revealButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const field = this.dataset.field;
                        const span = document.getElementById(field);

                        span.textContent = sensitiveData[field];

                        // Reset after 30 seconds
                        setTimeout(() => {
                            span.textContent = '********';
                        }, 30000);
                    });
                });
            });
        </script>
    @endpush
@endsection
