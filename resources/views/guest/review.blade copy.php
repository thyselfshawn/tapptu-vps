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
    <link href="{{ asset('vendor/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    @stack('head')
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
                <h2 class="offcanvas-title mb-3">Leave a Review</h2>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <!-- <a href="https://search.google.com/local/writereview?placeid=YOUR_PLACE_ID" class="btn btn-primary me-md-2"
                    target="_blank">
                    Leave a Google Review
                    </a> -->
                    <button class="btn btn-success btn-lg" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                    Leave a Google Review
                    </button>

                    <!-- Button 2: Another Review Link (e.g., Yelp) -->
                    <button class="btn btn-danger btn-lg" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                    Bad
                    </button>

                    <button class="btn btn-secondary btn-lg" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                    Good
                    </button>

                    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Tap To</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body">
                            <div id="contactForm">
                                <form action="{{ route('take_feedback', $venue->slug) }}" method="POST">
                                    @csrf
                                    <h6 class="offcanvas-title mb-3" id="offcanvasExampleLabel">Get something on next purchase!</h6>
                                    <div class="mb-3">
                                        <label for="review-text" class="form-label">Your name!</label>
                                        <input class="form-control" name="name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="review-text" class="form-label">Whatsapp number!</label>
                                        <input class="form-control" name="phone">
                                    </div>
                                    <button type="submit" class="btn btn-success">Validate Whatsapp</button>
                                    <button type="button" id="skipToReview" class="btn btn-warning">Skip to Review</button>
                                </form>
                            </div>

                            <!-- Review Form -->
                            <div id="reviewForm" style="display:none;">
                                <form action="" method="POST">
                                    @csrf
                                    <h6 class="offcanvas-title mb-3">Write your review!</h6>
                                    <div class="mb-3">
                                        <label for="review-text" class="form-label">Your name</label>
                                        <input type="text" class="form-control" name="name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="review-text" class="form-label">Phone number</label>
                                        <input type="text" class="form-control" name="phone">
                                    </div>
                                    <div class="mb-3">
                                        <label for="review-text" class="form-label">Message</label>
                                        <textarea class="form-control" name="message"></textarea>
                                    </div>
                                    <button type="button" id="goBack" class="btn btn-warning">Go Back</button>
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>


    <script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.bundle.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('#skipToReview').click(function(){
                $('#contactForm').hide();
                $('#reviewForm').show();
            });

            $('#goBack').click(function(){
                $('#reviewForm').hide();
                $('#contactForm').show();
            });
        });
    </script>
</body>
</html>
