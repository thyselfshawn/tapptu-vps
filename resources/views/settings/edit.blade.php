@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Settings</h1>
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="wa_number">WhatsApp Number</label>
                <input type="text" class="form-control" id="wa_number" name="wa_number" value="{{ $setting->wa_number }}"
                    required>
            </div>

            <div class="form-group">
                <label for="wa_instanceid">WhatsApp Instance ID</label>
                <input type="text" class="form-control" id="wa_instanceid" name="wa_instanceid"
                    value="{{ $setting->wa_instanceid }}" required>
            </div>

            <div class="form-group">
                <label for="wa_accesstoken">WhatsApp Access Token</label>
                <input type="text" class="form-control" id="wa_accesstoken" name="wa_accesstoken"
                    value="{{ $setting->wa_accesstoken }}" required>
            </div>

            <div class="form-group">
                <label for="stripe_publishable">Stripe Publishable Key</label>
                <input type="text" class="form-control" id="stripe_publishable" name="stripe_publishable"
                    value="{{ $setting->stripe_publishable }}" required>
            </div>

            <div class="form-group">
                <label for="stripe_secret">Stripe Secret Key</label>
                <input type="text" class="form-control" id="stripe_secret" name="stripe_secret"
                    value="{{ $setting->stripe_secret }}" required>
            </div>

            <div class="form-group">
                <label for="stripe_webhook_secret">Stripe Webhook Secret</label>
                <input type="text" class="form-control" id="stripe_webhook_secret" name="stripe_webhook_secret"
                    value="{{ $setting->stripe_webhook_secret }}" required>
            </div>

            <button type="submit" class="btn btn-primary">Update Settings</button>
        </form>
    </div>
@endsection
