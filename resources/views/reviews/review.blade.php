<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    @stack('head')
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <main class="py-4">
            <!-- Session Messages -->
            @if(session('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
            @endif
            
            <div class="container mt-5">
                <h1>Leave a Review</h1>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <!-- Button 1: Google Place Review -->
                    <a href="https://search.google.com/local/writereview?placeid=YOUR_PLACE_ID" class="btn btn-primary me-md-2" target="_blank">
                        Leave a Google Review
                    </a>

                    <!-- Button 2: Another Review Link (e.g., Yelp) -->
                    <button class="btn btn-success">
                        Not Good
                    </button>

                    <!-- Button 3: Trigger Modal -->
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#writeReviewModal">
                        Write Something
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="writeReviewModal" tabindex="-1" aria-labelledby="writeReviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="writeReviewModalLabel">Write a Review</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('take_feedback', $venue->slug) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="review-text" class="form-label">Tell us more!</label>
                            <textarea class="form-control" id="review-text" name="review" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('node_modules/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('node_modules/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script> <!-- Include your main JS file -->
    @stack('scripts')
</body>
</html>
