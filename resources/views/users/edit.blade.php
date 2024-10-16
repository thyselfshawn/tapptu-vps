@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Edit User</h4>
            <form class="forms-sample" action="{{ route('users.update', $user->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="form-group col-12">
                    <label for="name">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}"
                        required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group col-12">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}"
                        required>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Role -->
                <div class="form-group col-12">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="venue" {{ $user->role == 'venue' ? 'selected' : '' }}>Venue</option>
                    </select>
                    @error('role')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Avatar -->
                <div class="form-group col-12">
                    <label for="avatar">Avatar</label>
                    <input type="file" class="form-control form-control-file" id="avatar" accept="image/*">
                    <div class="mt-2">
                        <img id="avatar-preview" alt="Avatar Preview" class="img-thumbnail"
                            style="max-width: 200px; display: none;">
                        @if ($user->avatar)
                            <img src="{{ route('users.image', ['filename' => $user->avatar]) }}" alt="User Avatar"
                                id="oldavatar" class="img-fluid rounded-circle" style="max-width: 150px;" loading="lazy">
                        @endif
                    </div>
                    <input type="hidden" id="cropped-avatar" name="avatar">
                    @error('avatar')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group col-12" id="cropper-container" style="display: none;">
                    <label>Crop Avatar</label>
                    <div class="container">
                        <div class="img-container">
                            <img class="img-preview img-fluid" id="cropper-image" src="" alt="Avatar for cropping">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary mt-2" id="crop-button">Crop</button>
                </div>

                <!-- Submit and Cancel Buttons -->
                <button type="submit" class="btn btn-secondary mt-3">Update</button>
                <a href="{{ redirect()->back() }}" class="btn btn-warning mt-3">Cancel</a>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
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
        const oldavatar = document.getElementById('oldavatar');

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
                oldavatar.style.display = 'none'; // ghide the old avatar

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
