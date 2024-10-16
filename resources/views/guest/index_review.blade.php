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
    justify-content: center;
    align-items: center;
    height: 100%;
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
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

.button-container {
    display: flex;
    flex-direction: column;
    max-width: 380px;
    margin: 0 auto;
}

button {
    box-shadow: 0 5px 5px #a8a8a8;
}

.button-group {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    margin: 5vh 0;
}

.button-group .btn {
    width: 170px;
    height: 170px;
    border: 1px solid white;
    border-radius: 100%;
    display: flex;
    flex-direction: column;
    gap: 10px;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: normal;
    background-color: #f1f3f6;
    box-shadow: inset 0 0 15px rgba(55, 84, 170, 0),
    inset 0 0 20px rgba(255, 255, 255, 0),
    5px 15px 15px rgba(55, 84, 170, 0.507),
    inset 0px 0px 4px rgba(255, 255, 255, 0.2);
    transition: box-shadow 399ms ease-in-out;
    background: linear-gradient(315deg,
        hsl(232.73deg 20.5% 31.57%) 0%,
        hsl(248deg 100% 15.58% / 55%) 47%,
        hsl(255.29deg 42.21% 65.36%) 100%);
    /*
    background: linear-gradient(315deg, 
    hsl(213deg,95%,91%) 0.00%, 
    hsl(248deg,100%,80%) 47%,
    hsl(293deg,78%,89%) 100.00% 
    )
    */
}

.button-group .btn:hover {
    box-shadow: inset 7px 7px 15px rgba(55, 84, 170, 0.15),
    inset -7px -7px 20px white, 0px 0px 4px rgba(255, 255, 255, 0.2);
}

.button-group .btn-okay {
    background-color: #008100e0;
    color: lime;
}

.btn-okay span.icon {
    color: lime;
}

.button-group .btn-not-okay {
    background-color: #ff4400cc;
    color: #ffb426;
}

.btn-not-okay span.icon {
    color: #ffb426;
}

.button-group .btn span.icon {
    font-size: 2.5rem;
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

.btn-google .material-icons {
    color: gold;
    font-size: 2.5rem;
}

/* shiny button  styling */
.shiny-button {
    position: relative;
    padding: 25px 20px;
    font-size: 2rem;
    font-weight: bold;
    color: #fff;
    background-color: #333333;
    border: 1px solid gold;
    border-radius: 0.5rem;
    cursor: pointer;
    outline: none;
    overflow: hidden;
    /* Ensure the pseudo-element is contained */
    transition: background-color 1.5s ease, border 1s ease,
    box-shadow 1s ease;
    box-shadow: 0px 7px 15px -5px rgb(16, 22, 39);
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.shiny-button:hover {
    background-color: #000000;
    box-shadow: none;
}

.shiny-button:focus,
.shiny-button:active {
    outline: none;
    border: 1px solid transparent;
}

/* Shiny text effect */
.shiny-text {
    position: relative;
    display: inline-block;
    background: linear-gradient(90deg,
        #e9bb13 0%,
        #f1c73b 39%,
        #fee8a1 50%,
        #d4b550 61%,
        #9b8127 100%);
    background-size: 250% 100%;
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;

    transition: background-position 2s;
}

@keyframes move {
    0% {
    background-position: 200% 0%;
    }

    50% {
    background-position: -100% 0%;
    }
}

.shiny-button:hover .shiny-text {
    opacity: 1;
    background-position: 100% center;
    animation: move 3s ease;
}
</style>
@endpush
@section('content')
<div class="container-fluid">
    <div class="content text-center">
        <div class="text-content">
            <img class="logo-img" src="{{ asset('logo.png') }}" style="width:150px;height:150px;" />
            <p>Get wonderful rewards of discount oportunity by giving feedback</p>
        </div>

        <div class="button-container">
            <a href="{{ route('guest.create_voucher', ['venue' => $venue->slug, 'card' => $card->uuid, 'type' => 'google_feedback']) }}" class="shiny-button btn-google mt-5">
                <span class="material-icons">reviews</span>
                <span class="shiny-text">{{ __('Google Review') }}</span>
            </a>

            <div class="separator"></div>

            <div class="button-group">
                <a href="{{ route('guest.create_voucher', ['venue' => $venue->slug, 'card' => $card->uuid, 'type' => 'good_feedback']) }}" class="btn btn-okay">
                    <span class="icon far fa-laugh"></span>Okay
                </a>
                <a href="{{ route('guest.create_voucher', ['venue' => $venue->slug, 'card' => $card->uuid, 'type' => 'bad_feedback']) }}" class="btn btn-not-okay">
                    <span class="icon far fa-sad-tear"></span>Not Okay
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
