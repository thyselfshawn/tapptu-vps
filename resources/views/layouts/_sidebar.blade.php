<div class="offcanvas offcanvas-end sidebar-custom" data-bs-backdrop="static" tabindex="-1" id="staticBackdrop"
    aria-labelledby="staticBackdropLabel">
    <div class="offcanvas-header bg-primary text-white">
        <h5 class="offcanvas-title" id="staticBackdropLabel">{{ __('MENU') }}</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body bg-light">
        <div class="flex-shrink-0 p-3" style="width: 280px;">
            <ul class="list-unstyled sidebar-menu ps-0">
                @if (auth()->user()->role == 'admin' || auth()->user()->role == 'venue')
                    <li class="mb-1">
                        <button
                            class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed {{ Route::is('reports.*') ? 'active text-primary' : '' }}"
                            data-bs-toggle="collapse" data-bs-target="#home-collapse" aria-expanded="true">
                            {{ __('Reports') }}
                        </button>
                        <div class="collapse" id="home-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li>
                                    <a href="{{ route('reports.tap_report') }}"
                                        class="link-body-emphasis d-inline-flex text-decoration-none rounded {{ Route::currentRouteNamed('reports.tap_report') ? 'active text-primary' : '' }}">{{ __('Tap Report') }}
                                    </a>
                                </li>
                                <li class="mb-1">
                                    <button
                                        class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed {{ Route::is('reports.review_*') ? 'active text-primary' : '' }}"
                                        data-bs-toggle="collapse" data-bs-target="#growth-collapse"
                                        aria-expanded="true">
                                        {{ __('Growth Reports') }}
                                    </button>
                                    <div class="collapse" id="growth-collapse">
                                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                            <li>
                                                <a href="{{ route('reports.review_report') }}"
                                                    class="link-body-emphasis d-inline-flex text-decoration-none rounded {{ Route::currentRouteNamed('reports.review_report') ? 'active text-primary' : '' }}">{{ __('Review Report') }}</a>
                                            </li>
                                            <li>
                                                <a href="{{ route('reports.google_report') }}"
                                                    class="link-body-emphasis d-inline-flex text-decoration-none rounded {{ Route::currentRouteNamed('reports.google_report') ? 'active text-primary' : '' }}">{{ __('Google Report') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
                <li class="mb-1">
                    <button
                        class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed {{ Route::is('venues.*') ? 'active text-primary' : '' }}"
                        data-bs-toggle="collapse" data-bs-target="#dashboard-collapse" aria-expanded="false">
                        {{ __('Venues') }}
                    </button>
                    <div class="collapse" id="dashboard-collapse">
                        <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                            <li>
                                <a href="{{ route('venues.index') }}"
                                    class="link-body-emphasis d-inline-flex text-decoration-none rounded {{ Route::currentRouteNamed('venues.index') ? 'active text-primary' : '' }}">{{ __('All Venues') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('venues.create') }}"
                                    class="link-body-emphasis d-inline-flex text-decoration-none rounded {{ Route::currentRouteNamed('venues.create') ? 'active text-primary' : '' }}">
                                    {{ __('Create Venue') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="border-top my-3"></li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteNamed('vouchers.index') ? 'active text-primary' : '' }}"
                        href="{{ route('vouchers.index') }}"><i class="bi bi-boxes"></i> {{ __('Vouchers') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::currentRouteNamed('cards.index') ? 'active text-primary' : '' }}"
                        href="{{ route('cards.index') }}"><i class="bi bi-qr-code-scan"></i>
                        {{ __('RFID Cards') }}</a>
                </li>

                <li class="border-top my-3"></li>
                @if (auth()->user()->role == 'admin')
                    <li class="mb-3">
                        <a class="nav-link {{ Route::currentRouteNamed('subscriptions.index') ? 'active text-primary' : '' }}"
                            href="{{ route('subscriptions.index') }}"><i class="bi bi-safe2"></i>
                            {{ __('Subscriptions') }}</a>
                    </li>
                    <li class="mb-1">
                        <button
                            class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed {{ Route::is('packages.*') ? 'active text-primary' : '' }}"
                            data-bs-toggle="collapse" data-bs-target="#orders-collapse" aria-expanded="false">
                            {{ __('Packages') }}
                        </button>
                        <div class="collapse" id="orders-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li>
                                    <a href="{{ route('packages.index') }}"
                                        class="link-body-emphasis d-inline-flex text-decoration-none rounded {{ Route::currentRouteNamed('packages.index') ? 'active text-primary' : '' }}">{{ __('All Packages') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('packages.create') }}"
                                        class="link-body-emphasis d-inline-flex text-decoration-none rounded {{ Route::currentRouteNamed('packages.create') ? 'active text-primary' : '' }}">{{ __('Create Package') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="mb-1">
                        <button
                            class="btn btn-toggle d-inline-flex align-items-center rounded border-0 collapsed {{ Route::is('users.*') ? 'active text-primary' : '' }}"
                            data-bs-toggle="collapse" data-bs-target="#account-collapse" aria-expanded="false">
                            {{ __('Users') }}
                        </button>
                        <div class="collapse" id="account-collapse">
                            <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                                <li>
                                    <a href="{{ route('users.index') }}"
                                        class="link-body-emphasis d-inline-flex text-decoration-none rounded {{ Route::currentRouteNamed('users.index') ? 'active text-primary' : '' }}">{{ __('All Users') }}
                                    </a>
                                </li>
                                <li><a href="{{ route('users.create') }}"
                                        class="link-body-emphasis d-inline-flex text-decoration-none rounded {{ Route::currentRouteNamed('users.create') ? 'active text-primary' : '' }}">{{ __('Create User') }}</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteNamed('settings.show') || Route::currentRouteNamed('settings.edit') ? 'active text-primary' : '' }}"
                            href="{{ route('settings.show') }}"><i class="bi bi-qr-code-scan"></i>
                            {{ __('Settings') }}</a>
                    </li>
                @endif
                <hr>
                <!-- profile section -->
                <div class="dropdown">
                    <a href="#"
                        class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="{{ route('users.image', ['filename' => auth()->user()->avatar]) }}" alt="User Avatar"
                            class="rounded-circle me-2" width="32" height="32" loading="lazy">
                        <strong>{{ Auth::user()->name }}</strong>
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li>
                            <a class="dropdown-item"
                                href="{{ route('users.show', ['user' => auth()->user()]) }}">{{ __('Profile') }}
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </ul>
        </div>
    </div>
</div>
