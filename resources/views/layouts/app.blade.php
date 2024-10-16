<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'TappTu') }}</title>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        /* Color Palette */
        :root {
            --primary-color: #0044cc;
            --secondary-color: #002a80;
            --info-color: #d0e3ff;
            --dark-color: #002a80;
            --success-color: #007800;
            /* Custom Success Color */
            --danger-color: #a50000;
            /* Custom Danger Color */
            --warning-color: #ffcc00;
            /* Custom Warning Color */
        }

        /* Primary Button */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: #fff;
        }

        /* Secondary Button */
        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: var(--info-color);
            border-color: var(--secondary-color);
            color: var(--secondary-color);
        }

        /* Info Button */
        .btn-info {
            background-color: var(--info-color);
            border-color: var(--info-color);
            color: var(--dark-color);
            /* Dark text for contrast */
        }

        .btn-info:hover {
            background-color: darken(var(--info-color), 10%);
            border-color: darken(var(--info-color), 10%);
        }

        /* Success Button */
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
            color: #fff;
        }

        .btn-success:hover {
            background-color: darken(var(--success-color), 10%);
            border-color: darken(var(--success-color), 10%);
        }

        /* Danger Button */
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            color: #fff;
        }

        .btn-danger:hover {
            background-color: darken(var(--danger-color), 10%);
            border-color: darken(var(--danger-color), 10%);
        }

        /* Warning Button */
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            color: #002a80;
            /* Dark Color for Text */
        }

        .btn-warning:hover {
            background-color: darken(var(--warning-color), 10%);
            border-color: darken(var(--warning-color), 10%);
        }

        /* Outline Buttons */
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: #fff;
        }

        .btn-outline-secondary {
            color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
            color: #fff;
        }

        .btn-outline-info {
            color: var(--info-color);
            border-color: var(--info-color);
        }

        .btn-outline-info:hover {
            background-color: var(--info-color);
            color: var(--dark-color);
            /* Dark text for contrast */
        }

        .btn-outline-success {
            color: var(--success-color);
            border-color: var(--success-color);
        }

        .btn-outline-success:hover {
            background-color: var(--success-color);
            color: #fff;
        }

        .btn-outline-danger {
            color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .btn-outline-danger:hover {
            background-color: var(--danger-color);
            color: #fff;
        }

        .btn-outline-warning {
            color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .btn-outline-warning:hover {
            background-color: var(--warning-color);
            color: var(--dark-color);
            /* Dark text for contrast */
        }

        /* Form Group Styles */
        .form-group {
            margin-bottom: 1rem;
            /* Spacing below the form group */
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            border: 1px solid #d0e3ff !important;
            /* Shade color for border */
            border-radius: 4px;
            /* Rounded corners */
            background-color: #ffffff;
            /* White background */
            padding: 8px 12px;
            /* Padding inside the input/select/textarea */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            /* Shadow for 3D effect */
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
            /* Smooth shadow and border transition */
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #0044cc !important;
            /* Primary color for border on focus */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            /* Deeper shadow on focus */
        }

        .form-group label {
            color: #0044cc;
            /* Primary color for label text */
            font-weight: bold;
            /* Bold text for emphasis */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            /* Text shadow for 3D effect */
        }

        .form-group select {
            width: 100%;
            /* Ensure select box takes full width */
        }

        .form-group textarea {
            width: 100%;
            /* Ensure textarea takes full width */
            resize: vertical;
            /* Allow vertical resizing only */
        }

        /* Custom Navbar Styles */
        .navbar {
            background-color: #d0e3ff !important;
            /* Your theme color */
            border-radius: 8px;
            /* Rounded corners for a subtle 3D effect */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            /* Shadow for depth */
            transition: box-shadow 0.3s ease-in-out;
            /* Smooth shadow transition */
        }

        .navbar:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2), 0 2px 4px rgba(0, 0, 0, 0.15);
            /* Enhanced shadow on hover */
        }

        .navbar-brand,
        .nav-link {
            color: #0044cc;
            /* Optional: Adjust text color to contrast with background */
        }

        .navbar-brand {
            font-size: 1.25rem;
            /* Adjust font size if needed */
        }

        /* Custom Navbar Toggler Button */
        /* Custom Navbar Toggler Button */
        .menu-toggler {
            background-color: #002a80;
            /* Darker shade of the primary color for contrast */
            color: #ffffff;
            /* Text color for the toggler button */
            border: none;
            /* Remove border for a cleaner look */
            padding: 6px 12px;
            /* Padding similar to DataTable buttons */
            border-radius: 4px;
            /* Rounded corners for consistency */
            transition: background-color 0.3s ease;
            /* Smooth transition for hover effect */
        }

        .menu-toggler:hover {
            background-color: #002a80;
            /* Even darker shade for hover effect */
        }

        /* Custom Navbar Toggler Icon */
        .menu-toggler-icon {
            filter: brightness(0) invert(1);
            /* Ensure the toggle icon contrasts with the background */
        }

        /* Existing Card Styles */
        /* Existing Card Styles */
        .card {
            background-color: #ffffff;
            /* Flat white background */
            border: 2px solid #d0e3ff;
            /* Shade color border, thicker to ensure visibility */
            border-radius: 8px;
            /* Rounded corners for a subtle 3D effect */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            /* Shadow for depth */
            transition: box-shadow 0.3s ease-in-out, border-color 0.3s ease-in-out;
            /* Smooth transition for shadow and border color */
        }

        /* Card Header */
        .card-header {
            background-color: #d0e3ff;
            /* Shade color for the card header */
            border-bottom: 2px solid #d0e3ff;
            /* Same shade color border, thicker for visibility */
            color: #0044cc;
            font-weight: bold;
            font-size: 18px;
            /* Optional: Adjust text color for contrast */
        }

        /* Card Body */
        .card-body {
            padding: 1.25rem;
            /* Standard padding */
        }

        /* Custom Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 10px;
            background-color: #d0e3ff;
            /* Shade color */
        }

        ::-webkit-scrollbar-track {
            border: 1px solid #002a80;
            /* Darker color */
            background-color: #d0e3ff;
            /* Shade color */
        }

        ::-webkit-scrollbar-thumb {
            background-color: #0044cc;
            /* Primary color */
            border-radius: 5px;
            /* Optional: Adds rounded corners to the scrollbar thumb */
        }

        .img-container {
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            /* Hide overflow to ensure the image doesn't exceed the container */
        }

        /* Large screens (e.g., desktops) */
        @media (min-width: 1200px) {
            .img-container {
                height: 500px;
                /* Fixed height for large screens */
            }
        }

        /* Medium screens (e.g., tablets) */
        @media (min-width: 768px) and (max-width: 1199px) {
            .img-container {
                height: 400px;
                /* Fixed height for medium screens */
            }
        }

        /* Small screens (e.g., phones) */
        @media (max-width: 767px) {
            .img-container {
                height: 300px;
                /* Fixed height for small screens */
            }
        }

        .img-preview {
            width: 100%;
            /* Ensure the image fits within the container width */
            height: 100%;
            /* Fill the container height */
            object-fit: cover;
            /* Maintain aspect ratio while filling the container */
        }

        /* Optional: Customize scrollbar for specific elements */
        .scrollable-element {
            scrollbar-width: thin;
            /* For Firefox */
            scrollbar-color: #0044cc #d0e3ff;
            /* Primary and Shade colors for Firefox */
        }

        /* General table styling */
        .table {
            border: 2px solid #d0e3ff;
            /* Shade color border around the table */
            border-radius: 8px;
            /* Rounded corners for a 3D effect */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* Shadow for depth */
            width: 100% !important;
            /* Force table to use full width */
            table-layout: auto;
            /* Automatically adjust column widths */
        }

        .table-responsive {
            width: 100%;
            /* Ensure the container uses full width */
            overflow-x: auto;
            /* Enable horizontal scroll if needed */
        }

        .table thead th {
            background-color: #d0e3ff;
            /* Shade color for the table header */
            border-bottom: 2px solid #d0e3ff;
            /* Matching border color for a 3D effect */
            color: #0044cc;
            /* Text color for the table header */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Shadow for depth */
        }

        /* Table striped background colors */
        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #d0e3ff !important;
            /* White background for odd rows */
        }

        .table-striped>tbody>tr:nth-of-type(even) {
            background-color: #ffffff !important;
            /* Shade color for even rows */
        }

        /* Optional: Enhance hover effect */
        .table-striped>tbody>tr:hover {
            background-color: #c0d4ff !important;
            /* Slightly darker shade on hover */
        }

        /* Ensure that table cells align with the background */
        .table-striped>tbody>tr>* {
            background-color: transparent !important;
            /* Make sure cells do not override row background */
        }


        .dataTables_wrapper .dataTables_filter input,
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #d0e3ff;
            /* Border color for filter and length select */
            border-radius: 4px;
            color: #0044cc;
            /* Text color for input fields */
        }

        .dataTables_wrapper .dataTables_filter label,
        .dataTables_wrapper .dataTables_length label {
            color: #0044cc;
            font-size: 18px;
            /* Text color for labels */
        }

        .dataTables_wrapper .dataTables_sorting,
        .dataTables_wrapper .dataTables_sorting_asc,
        .dataTables_wrapper .dataTables_sorting_desc {
            color: #0044cc;
            /* Text color for sorting icons */
        }

        /* DataTable Buttons */
        .dataTables_wrapper .dt-buttons .btn {
            background-color: #0044cc;
            /* Primary color for DataTable buttons */
            color: #ffffff;
            /* Text color for DataTable buttons */
            border: none;
            padding: 6px 12px;
            border-radius: 2px;
            /* Rounded corners for consistency */
            transition: background-color 0.3s ease;
            /* Smooth transition for hover effect */
        }

        .dataTables_wrapper .dt-buttons .btn:hover {
            background-color: #002a80;
            /* Slightly darker shade on hover */
        }

        /* DataTables search box and sort box styles */
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d0e3ff !important;
            /* Shade color for border */
            border-radius: 4px;
            /* Rounded corners */
            background-color: #ffffff;
            /* White background */
            padding: 8px 12px;
            /* Padding inside the input */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            /* Shadow for 3D effect */
            transition: box-shadow 0.3s ease;
            /* Smooth shadow transition */
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #0044cc !important;
            /* Primary color for border on focus */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            /* Deeper shadow on focus */
        }

        .dataTables_wrapper .dataTables_filter label {
            color: #0044cc;
            /* Primary color for label text */
            font-weight: bold;
            /* Bold text for emphasis */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            /* Text shadow for 3D effect */
        }

        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #d0e3ff !important;
            /* Shade color for border */
            border-radius: 4px;
            /* Rounded corners */
            background-color: #ffffff;
            /* White background */
            padding: 6px 12px;
            /* Padding inside the select box */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            /* Shadow for 3D effect */
            transition: box-shadow 0.3s ease;
            /* Smooth shadow transition */
            font-size: 14px;
            /* Adjust font size for better alignment */
        }

        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #0044cc !important;
            /* Primary color for border on focus */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            /* Deeper shadow on focus */
        }

        .dataTables_wrapper .dataTables_info {
            color: #0044cc;
            /* Primary color for text */
            font-weight: bold;
            /* Bold text for emphasis */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            /* Text shadow for 3D effect */
        }

        /* DataTables sort icons */
        .dataTables_wrapper .dataTables_sorting,
        .dataTables_wrapper .dataTables_sorting_desc,
        .dataTables_wrapper .dataTables_sorting_asc {
            position: relative;
            /* Ensure positioning context for icon */
            padding-right: 25px;
            /* Add padding to the right for spacing */
            color: #0044cc;
            /* Primary color for text */
            font-size: 16px;
            /* Adjust font size as necessary */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            /* Shadow for 3D effect */
            border-radius: 4px;
            /* Rounded corners */
            background-color: #ffffff;
            /* White background */
            transition: box-shadow 0.3s ease;
            /* Smooth shadow transition */
        }

        .dataTables_wrapper .dataTables_sorting:hover,
        .dataTables_wrapper .dataTables_sorting_desc:hover,
        .dataTables_wrapper .dataTables_sorting_asc:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            /* Deeper shadow on hover */
        }

        /* Add spacing for sort icon */
        .dataTables_wrapper .dataTables_sorting::after,
        .dataTables_wrapper .dataTables_sorting_desc::after,
        .dataTables_wrapper .dataTables_sorting_asc::after {
            content: '';
            /* Empty content to use for positioning */
            position: absolute;
            right: 10px;
            /* Adjust spacing from the right */
            top: 50%;
            transform: translateY(-50%);
            font-size: 12px;
            /* Adjust size as necessary */
            color: #0044cc;
            /* Primary color for icon */
        }

        /* Specific icons for sorting states */
        .dataTables_wrapper .dataTables_sorting::after {
            content: '\f0dc';
            /* Example sort icon (FontAwesome) */
            font-family: 'FontAwesome';
            /* Adjust as per your icon font */
        }

        .dataTables_wrapper .dataTables_sorting_desc::after {
            content: '\f0de';
            /* Example descending icon (FontAwesome) */
            font-family: 'FontAwesome';
            /* Adjust as per your icon font */
        }

        .dataTables_wrapper .dataTables_sorting_asc::after {
            content: '\f0db';
            /* Example ascending icon (FontAwesome) */
            font-family: 'FontAwesome';
            /* Adjust as per your icon font */
        }

        /* DataTables length control styling */
        .dataTables_wrapper .dataTables_length {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            /* Space below the length control */
            color: #0044cc;
            /* Primary color for text */
        }

        /* Length label styling */
        .dataTables_wrapper .dataTables_length label {
            font-size: 16px;
            /* Font size for the label */
            margin-right: 16px;
            /* Space between label and select box */
            color: #0044cc;
            /* Primary color for the label text */
        }

        /* Length select box styling */
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #d0e3ff;
            /* Shade color for the border */
            border-radius: 4px;
            /* Rounded corners */
            padding: 6px 10px;
            /* Padding inside the select box */
            font-size: 16px;
            /* Font size for the select box */
            color: #0044cc;
            /* Primary color for the text inside the select box */
            background-color: #ffffff;
            /* White background for the select box */
            appearance: none;
            /* Remove default dropdown arrow */
            width: 80px;
            /* Increase the width of the select box */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Shadow for 3D effect */
            transition: box-shadow 0.3s ease;
            /* Smooth shadow transition */
        }

        /* Length select box hover effect */
        .dataTables_wrapper .dataTables_length select:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            /* Deeper shadow on hover */
        }

        /* Custom dropdown icon */
        .dataTables_wrapper .dataTables_length select::-ms-expand {
            color: #0044cc;
            /* Primary color for the dropdown arrow */
        }

        /* Add a custom style for the dropdown arrow */
        .dataTables_wrapper .dataTables_length select::after {
            content: '\25BC';
            /* Downward arrow */
            font-size: 16px;
            /* Size of the arrow */
            color: #0044cc;
            /* Theme color for the arrow */
            position: absolute;
            /* Position the arrow */
            right: 10px;
            /* Space between the arrow and the right edge */
            top: 50%;
            /* Center the arrow vertically */
            transform: translateY(-50%);
            /* Center alignment */
        }

        /* Ensure select box and icon are not hugging each other */
        .dataTables_wrapper .dataTables_length {
            position: relative;
            /* Position relative to position the custom arrow */
        }

        .dataTables_wrapper .dataTables_length select {
            padding-right: 24px;
            /* Extra space for the dropdown icon */
        }

        /* Adjustments for larger screens */
        @media (min-width: 992px) {
            .dataTables_wrapper .dataTables_length {
                margin-bottom: 0;
                /* Adjust spacing for larger screens if needed */
            }
        }

        /* Base styling for all li elements in the sidebar with increased font size */
        .sidebar-custom .sidebar-menu li {
            font-size: 18px;
            /* Increased font size */
            color: #002a80;
            /* Dark color */
            padding: 0.5rem 0;
            margin: 0;
        }

        .sidebar-custom .sidebar-menu li i {
            margin-left: 12px;
        }

        /* Apply margin for child hierarchy */
        .sidebar-custom .sidebar-menu li ul {
            margin-left: 15px;
            /* Minimal margin start for child items */
        }

        /* Styling for nested li elements within ul */
        .sidebar-custom .sidebar-menu li ul li {
            font-size: 16px;
            /* Smaller font size for nested items */
            font-weight: bold;
            /* Bold font for nested items */
        }

        /* Collapsible items should also push their children to the left */
        .sidebar-custom .sidebar-menu li .collapse ul {
            margin-left: 15px;
        }

        /* Style for links within list items */
        .sidebar-custom .sidebar-menu a {
            text-decoration: none;
            color: #0044cc;
            /* Primary color */
            transition: color 0.3s;
        }

        .sidebar-custom .sidebar-menu a:hover {
            color: #d0e3ff;
            /* Shade color on hover */
        }

        /* Style for active items */
        .sidebar-custom .sidebar-menu .active {
            font-weight: bold;
            color: #0044cc;
            /* Primary color */
        }

        /* Styling for buttons and collapsible headers */
        .sidebar-custom .btn-toggle {
            font-size: 16px;
            /* Match the main list item font size */
            background-color: transparent;
            border: none;
            color: #0044cc;
            /* Primary color */
            cursor: pointer;
            transition: color 0.3s;
        }

        .sidebar-custom .btn-toggle:hover {
            color: #d0e3ff;
            /* Shade color */
        }

        /* Dropdown styling */
        .sidebar-custom .dropdown-menu {
            background-color: #002a80;
            /* Dark color */
            color: #d0e3ff;
            /* Shade color */
        }

        /* Avatar and User Section */
        .sidebar-custom .dropdown-toggle img {
            border: 2px solid #0044cc;
            /* Primary color */
        }

        .sidebar-custom .dropdown-toggle strong {
            color: #002a80;
            /* Dark color */
        }

        /* Divider */
        .sidebar-custom hr {
            border-color: #0044cc;
            /* Primary color for divider */
        }


        /* Base styling for the filter-canvas */
        .offcanvas {
            background-color: #d0e3ff;
            /* Shade color for the canvas background */
            color: #002a80;
            /* Dark color for text */
            width: 280px;
            /* Adjust to match sidebar width */
            border: 1px solid #0044cc;
            /* Primary color border */
        }

        /* Header styling in the filter-canvas */
        .offcanvas .offcanvas-header {
            background-color: #0044cc;
            /* Primary color */
            color: #fff;
            /* White text color */
        }

        .offcanvas .offcanvas-header .btn-close {
            filter: invert(1);
            /* White close button */
        }

        .form-label {
            color: #002a80;
            /* Dark color for labels */
            font-weight: bold;
            font-size: 16px;
            /* Bold labels */
        }

        .form-select,
        .form-control {
            background-color: #fff;
            /* White background for inputs */
            border: 1px solid #0044cc;
            /* Primary color border */
            color: #002a80;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            /* Dark color for text */
        }

        .form-select:focus,
        .form-control:focus {
            border-color: #0044cc;
            /* Primary color on focus */
            box-shadow: 0 0 0 0.2rem rgba(0, 68, 204, 0.25);
            /* Subtle shadow */
        }

        /* Radio button styling */
        .btn-group .btn-check:checked+.btn-secondary,
        .btn-group .btn-secondary.active {
            background-color: #0044cc;
            /* Primary color */
            border-color: #0044cc;
            color: #fff;
            /* White text */
        }

        .btn-group .btn-secondary {
            background-color: #fff;
            /* White background for radio buttons */
            border-color: #0044cc;
            /* Primary color border */
            color: #002a80;
            /* Dark color text */
        }

        /* Hover state for radio buttons */
        .btn-group .btn-secondary:hover {
            background-color: #f0f4ff;
            /* Light shade for hover */
            border-color: #0044cc;
            color: #0044cc;
            /* Primary color border */
        }
    </style>
</head>

<body>
    <div class="container-fluid" id="app">
        @include('layouts._header')
        <div class="row">
            <main class="col-12 pt-4">
                @auth
                    @include('layouts._sidebar')
                    @if (auth()->check() && auth()->user()->role === 'venue')
                        @php
                            $venues = auth()->user()->venues;
                        @endphp

                        @foreach ($venues as $item)
                            @if ($item->currentMembership() && $item->currentMembership()->isEndingSoon(7))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ $item->name }}'s membership ends in
                                    {{ $item->currentMembership()->timeUntilEnds() }}.
                                    Please renew soon!
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                        @endforeach
                    @endif

                @endauth
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
        @stack('scripts')
    </div>
</body>

</html>
