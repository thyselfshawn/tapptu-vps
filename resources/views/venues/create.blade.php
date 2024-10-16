@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">Create Venue</div>
        <div class="card-body">
            <form action="{{ route('venues.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    @if (auth()->user()->role == 'admin')
                        <div class="from-group col-6 mb-2">
                            <label for="user_id" class="form-label">User</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id"
                                name="user_id">
                                @foreach ($users as $item)
                                    <option value="{{ $item->id }}" {{ old('user_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="from-group col-6 mb-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                @foreach (App\Enums\VenueStatusEnum::cases() as $status)
                                    <option value="{{ $status->value }}"
                                        {{ old('status') == $status->value ? 'selected' : '' }}>
                                        {{ ucfirst($status->value) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    @endif
                    @if (request()->query('card'))
                        <input type="text" name="card" value="{{ request()->query('card') }}" hidden>
                    @else
                        <input type="text" name="card" value="" hidden>
                    @endif
                    <input id="place" type="text" name="googleplaceid" hidden />
                    <input id="review" type="text" name="googlereviewstart" hidden />

                    <div class="form-group col-12 mb-2" id="cropper-container" style="display: none;">
                        <label>Crop Avatar</label>
                        <div class="img-container">
                            <img class="img-preview img-fluid" id="cropper-image" src="" alt="Avatar for cropping">
                        </div>
                        <button type="button" class="btn btn-primary mt-2" id="crop-button">Crop</button>
                    </div>

                    <div class="from-group col-6 mb-2">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" placeholder="Enter Venue Name" required autocomplete="off">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="from-group col-6 mb-2">
                        <label for="avatar" class="form-label">Logo</label>
                        <input type="file" class="form-control form-control-file" id="avatar" accept="image/*">
                        <div class="mt-2">
                            <img id="avatar-preview" alt="Avatar Preview" class="img-thumbnail"
                                style="max-width: 200px; display: none;">
                        </div>
                        <input type="hidden" id="cropped-avatar" name="logo">
                        @error('logo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="from-group col-12 mb-2">
                        <label for="voucher" class="form-label">Voucher Text</label>
                        <textarea type="text" class="form-control @error('voucher') is-invalid @enderror" id="voucher" name="voucher"
                            placeholder="Get 10% discount!">{{ old('voucher') }}</textarea>
                        @error('voucher')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label d-block mb-2">Weekly email report</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" name="notification" id="notificationToggle"
                                value="1" {{ old('notification') == 1 ? 'checked' : 'Off' }}>
                            <label class="form-check-label" for="notificationToggle">
                                {{ old('notification') == 1 ? 'On' : 'Off' }}
                            </label>
                        </div>
                    </div>
                    <div class="col-4"><button type="submit" class="btn btn-secondary">Create</button></div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCO34vxnjcT_NlL8oP6BtF-A2E9AqN2u-k&callback=initAutocomplete&libraries=places&v=weekly"
        defer></script>

    <script>
        let autocomplete;
        let nameField;
        let placeField;
        let reviewField;

        function initAutocomplete() {
            nameField = document.querySelector("#name");
            placeField = document.querySelector("#place");
            reviewField = document.querySelector("#review");

            autocomplete = new google.maps.places.Autocomplete(nameField, {
                fields: ["address_components", "geometry", "place_id", "user_ratings_total"]
            });
            nameField.focus();
            autocomplete.addListener("place_changed", fillInAddress);
        }

        function fillInAddress() {
            const place = autocomplete.getPlace();
            placeField.value = place.place_id;
            reviewField.value = place.user_ratings_total;
            document.querySelector("#avatar").focus();
        }

        window.initAutocomplete = initAutocomplete;
    </script>

    <!-- Include Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

    <script>
        let cropper;
        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.getElementById('avatar-preview');
        const cropperContainer = document.getElementById('cropper-container');
        const cropperImage = document.getElementById('cropper-image');
        const croppedAvatarInput = document.getElementById('cropped-avatar');

        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    cropperImage.src = event.target.result;
                    cropperContainer.style.display = 'block';

                    // Initialize or replace the cropper
                    if (cropper) {
                        cropper.replace(event.target.result);
                    } else {
                        cropper = new Cropper(cropperImage, {
                            aspectRatio: 1,
                            viewMode: 3,
                        });
                    }
                };
                reader.readAsDataURL(file);
            }
        });

        document.getElementById('crop-button').addEventListener('click', function() {
            const canvas = cropper.getCroppedCanvas({
                width: 200,
                height: 200,
            });
            canvas.toBlob(function(blob) {
                const url = URL.createObjectURL(blob);
                avatarPreview.src = url;
                avatarPreview.style.display = 'block'; // Show the avatar preview

                const reader = new FileReader();
                reader.onloadend = function() {
                    croppedAvatarInput.value = reader.result;
                };
                reader.readAsDataURL(blob);

                // Hide the cropper after cropping
                cropperContainer.style.display = 'none';
            });
        });
    </script>
@endpush
