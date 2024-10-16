@extends('guest.layout')
@push('head')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" />

    <style>
        * {
            letter-spacing: 0.5px;
        }

        html,
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-attachment: fixed;
            background-image: radial-gradient(circle at 50% 0%,
                    rgb(253 233 255) 16.4%,
                    rgb(107 119 205) 68.2%,
                    rgb(41 41 41) 99.1%);
        }

        .container-fluid {
            display: flex;
            flex-direction: column;

            align-items: center;
            height: 100%;
            padding-top: 1.5rem;
        }

        .content .text-content {
            background: linear-gradient(90deg,
                    #00000024 -30%,
                    transparent 50%,
                    #00000024 130%);
            padding: 20px 10px;
            border: 1px solid #d1d1d1;
            border-radius: 0.5rem;
        }

        .content h1 {
            background: linear-gradient(23deg,
                    #28e3b3,
                    #2892e3,
                    #7b28e3,
                    #e3286c,
                    #28e3d9,
                    #aaa400,
                    #6011f3,
                    #e3a228);
            background-size: 1600% 1600%;
            -webkit-animation: AnimationName 30s ease infinite;
            -moz-animation: AnimationName 30s ease infinite;
            -o-animation: AnimationName 30s ease infinite;
            animation: AnimationName 30s ease infinite;

            text-align: center;
            font-size: 3rem;
            font-weight: bold;
            color: #000;
            background-clip: text;
            text-fill-color: transparent;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        @-webkit-keyframes AnimationName {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        @keyframes AnimationName {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .text-content p {
            font-size: 1.5rem;
        }

        .voucher-form-container {
            display: flex;
            flex-direction: column;
            padding: 10px;
            margin-top: 2rem;
            background-color: #2122351c;

            border-radius: 0.5rem;
            border: none;
        }

        .form-group {
            position: relative;
        }

        .form-group.with-icon .form-control {
            padding-left: 2.375rem;
            width: 100%;
            background: rgba(0, 0, 0, 0.3);
            outline: none;
            font-size: 1rem;
            color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.3);
            border-radius: 4px;
            box-shadow: inset 0 -5px 45px rgba(100, 100, 100, 0.2),
                0 1px 1px rgba(255, 255, 255, 0.2);
            -webkit-transition: box-shadow 0.5s ease;
            -moz-transition: box-shadow 0.5s ease;
            -o-transition: box-shadow 0.5s ease;
            -ms-transition: box-shadow 0.5s ease;
            transition: box-shadow 0.5s ease;
        }

        .form-group.with-icon .form-control::placeholder {
            color: #d3d3d3;
            font-style: normal;
            opacity: 1;
        }

        .form-group.with-icon .form-control:focus {
            box-shadow: inset 0 -5px 45px rgba(0, 0, 0, 0.4),
                0 1px 1px rgba(255, 255, 255, 0.2);
        }

        .form-group.with-icon span {
            position: absolute;
            z-index: 1;
            display: block;
            width: 2.375rem;
            height: 2.375rem;
            line-height: 2.3rem;
            text-align: center;
            pointer-events: none;
            color: #d3d3d3;
        }

        button.btn.btn-skip {
            box-shadow: 0 5px 15px -8px black;
            background-color: #00000000;
            border: 2px solid #dddddd;
            color: white;
        }

        button.btn.btn-submit {
            box-shadow: 0 5px 15px -3px black;
            background-color: black;
            color: white;

            transform: scale(1) translateZ(0);
            transition: transform 0.1s ease-in-out,
                background-color 0.1s ease-in-out, box-shadow 0.3s ease-in-out;
            -webkit-transform-style: preserve-3d;
            -webkit-backface-visibility: hidden;
        }

        button.btn.btn-submit:hover,
        .btn-submit:active {
            box-shadow: none;
            background-color: #383838;
            transform: scale(0.96) translateZ(0);
            -webkit-transform-style: preserve-3d;
            -webkit-backface-visibility: hidden;
        }

        button.btn span {
            font-size: 1rem;
            margin-right: 0.75rem;
            -webkit-transform-style: preserve-3d;
            -webkit-backface-visibility: hidden;
        }

        .button-group {
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .button-group button {
            width: 50%;
        }

        @media (min-width: 768px) {
            .button-group {
                justify-content: space-evenly;
            }
        }

        .separator {
            height: 5px;
            margin: 45px 0 0 0;
            width: 100%;
            border: none;
            border-top: 2px solid;
            border-image-source: linear-gradient(to right,
                    transparent 5%,
                    #fff700,
                    transparent 95%);
            border-image-slice: 1;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="content text-center">
            <div class="text-content">
                <img class="logo-img" src="{{ asset('logo.png') }}" style="width:150px;height:150px;" />
                <p>Enter your name & Whatsapp number to receive a free cocktail</p>
            </div>
            <div class="voucher-form-container">
                <form action="{{ route('guest.store_voucher', ['venue' => $venue->slug, 'card' => $card->uuid]) }}"
                    method="POST">
                    @csrf
                    <div class="form-group mb-2 with-icon">
                        <span class="far fa-user"></span>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" placeholder="Enter full name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group mb-2 with-icon">
                        <span class="fab fa-whatsapp"></span>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                            name="phone" placeholder="With country code and no symbol" value="{{ old('phone') }}"
                            required>
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="button-group">
                        @if (session('review-type') == 'google_feedback')
                            @php
                                $url = 'https://search.google.com/local/writereview?placeid=' . $venue->googleplaceid;
                            @endphp
                            <a href="{{ $url }}" class="btn btn-skip">
                                <span class="icon fas fa-undo-alt"></span>Skip
                            </a>
                        @else
                            <a href="{{ route('guest.create_review', ['venue' => $venue->slug, 'card' => $card->uuid]) }}"
                                class="btn btn-skip">
                                <span class="icon fas fa-undo-alt"></span>Skip
                            </a>
                        @endif
                        <button type="submit" class="btn btn-submit">
                            <span class="icon fas fa-paper-plane"></span>Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush
