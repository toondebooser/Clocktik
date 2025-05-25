<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100;200;300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="manifest"
        href="{{ url('/manifest.json') }}?v={{ auth()->check() ? auth()->user()->company->updated_at->timestamp : time() }}">

    @php

        $logoPath = auth()->user()?->company?->company_logo
            ? asset(auth()->user()->company?->company_logo)
            : asset('images/TaxusLogo.png');
        $themeColor = auth()->user()?->company?->company_color ?? '#4FAAFC';

    @endphp

    <!-- Favicon for all browsers -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $logoPath }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $logoPath }}">
    <link rel="icon" type="image/png" sizes="48x48" href="{{ $logoPath }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ $logoPath }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ $logoPath }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ $logoPath }}">

    <!-- Apple Touch Icons (for iOS homescreen) -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ $logoPath }}">
    <link rel="apple-touch-icon" sizes="512x512" href="{{ $logoPath }}">

    <!-- Apple-specific meta -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Werkuren.be">
    <meta name="apple-mobile-web-app-status-bar-style" content="{{ $themeColor }}">

    <!-- Android Chrome metadata -->
    <meta name="theme-color" content="{{ $themeColor }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Werkuren.be">


    {{-- <link rel="stylesheet" href="{{ asset('public/build/assets//app-d725fd8f.css') }}"> --}}
    @vite('resources/css/app.css')
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 11;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            place-items: center;
        }

        .modal-content {
            /* display:flex;
            flex-direction:column; */
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            width: 70%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .modal-button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .modal-button:hover {
            background-color: #0056b3;
        }

        .modal-button.cancel {
            background-color: #6c757d;
        }

        .modal-button.cancel:hover {
            background-color: #5a6268;
        }

        .close {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }

        .message {

            animation: fadeIn 0.3s ease-out forwards;

        }



        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.error ul {
            margin: 0;
            padding-left: 20px;
            list-style-type: disc;
        }

        .message.error li {
            margin-bottom: 5px;
        }








        /* Fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }






        :root {
            --primary-color: {{ auth()->user()?->company?->company_color ?? '#4FAAFC' }};



        }
    </style>


    <title>Werkuren</title>



</head>

<body>

    <?php
    $currentUser = auth()->user();
    ?>
    <div class="bodyContent">
        <header>
            <a class="headerLinks" href="{{ route('home') }}">Home</a>
            @auth
                <div id="side-menu" class="side-menu">
                    <a style=" height: 40px;"
                        href="{{ route('adminSettings', ['company_code' => $currentUser->company_code]) }}">
                        <img style="height: inherent" src="{{ asset('images/settings.png') }}" alt="settings">
                    </a>
                    <a class="headerLinks" href="{{ route('dashboard') }}">Timeclock</a>
                    @if ($currentUser->god)
                        <a class="authLinks button" href="{{ route('saurons-eye') }}">Logs</a>
                        <a class="authLinks button"
                            href="{{ route('myList', ['type' => 'Bedrijven', 'company_code' => $currentUser->company_code]) }}">Bedrijven</a>
                    @else
                        <a class="authLinks button"
                            href="{{ route('myList', ['type' => 'Personeel', 'company_code' => $currentUser->company_code]) }}">Personeel</a>
                    @endif
                </div>
                <div class="backdrop"></div>

                @if ($currentUser->admin && $currentUser->company->admin_timeclock)
                    <div class="browserHeader">
                        <a href="{{ route('adminSettings', ['company_code' => $currentUser->company_code]) }}">
                            <img style="height: 40px" src="{{ asset('images/settings.png') }}" alt="settings">
                        </a>
                        <a class="headerLinks" href="{{ route('dashboard') }}">Timeclock</a>
                        @if (!$currentUser->god)
                            <a class="authLinks button"
                                href="{{ route('myList', ['type' => 'Personeel', 'company_code' => $currentUser->company_code]) }}">Personeel</a>
                        @else
                            <a class="authLinks button"
                                href="{{ route('myList', ['type' => 'Bedrijven', 'company_code' => $currentUser->company_code]) }}">Bedrijven</a>
                        @endif
                    </div>
                    <div class="headerLinks " id="nav-icon4" onclick="toggle()">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                @elseif ($currentUser->god)
                    <a href="{{ route('adminSettings', ['company_code' => $currentUser->company_code]) }}">
                        <img style="height: 40px" src="{{ asset('images/settings.png') }}" alt="settings">
                    </a>
                    <a class="authLinks button" href="{{ route('saurons-eye') }}">Logs</a>
                    <a class="authLinks button"
                        href="{{ route('myList', ['type' => 'Bedrijven', 'company_code' => $currentUser->company_code]) }}">Bedrijven</a>
                @elseif (!$currentUser->admin)
                    <a class="headerLinks" href="{{ route('dashboard') }}">Timeclock</a>
                    @if ($currentUser->admin && !$currentUser->god)
                        <a href="{{ route('adminSettings', ['company_code' => $currentUser->company_code]) }}">
                            <img style="height: 40px" src="{{ asset('images/settings.png') }}" alt="settings">
                        </a>
                        <a class="authLinks button"
                            href="{{ route('myList', ['type' => 'Personeel', 'company_code' => $currentUser->company_code]) }}">Personeel</a>
                    @endif
                    <a class="authLinks button" href="{{ route('myProfile') }}">Mijn profiel</a>
                @else
                    <a href="{{ route('adminSettings', ['company_code' => $currentUser->company_code]) }}">
                        <img style="height: 40px" src="{{ asset('images/settings.png') }}" alt="settings">
                    </a>
                    <a class="authLinks button"
                        href="{{ route('myList', ['type' => 'Personeel', 'company_code' => $currentUser->company_code]) }}">Personeel</a>
                @endif
                <a class="authLinks button" href="{{ route('logout') }}">Logout</a>

            @endauth
            @guest
                <a class="authLinks button" href="{{ route('login') }}">Login</a>
            @endguest
        </header>
        {{-- @yield('error') --}}
        @yield('title')
        @yield('content')
        @if (session('success') || $errors->any() || session('status') || session('verified'))
            <div class="message">
                @if (session('success'))
                    <div class="success">
                        <img style="height: 50px" src="{{ asset('images/success.png') }}" alt="succes">
                        {{ session('success') }}
                        <a href="#" class="removeError" onclick="closeMessage(this)">Sluiten</a>
                    </div>
                @endif
                @if (session('status'))
                    <div class="success">
                        <img style="height: 50px" src="{{ asset('images/success.png') }}" alt="succes">
                        {{ session('status') }}
                        <a href="#" class="removeError" onclick="closeMessage(this)">Sluiten</a>
                    </div>
                @endif
                @if (session('verified'))
                    <div class="success">
                        <img style="height: 50px" src="{{ asset('images/success.png') }}" alt="succes">
                        Bedankt voor het verifiëren van uw e-mail!
                        <a href="#" class="removeError" onclick="closeMessage(this)">Sluiten</a>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="error">
                        <img style="height: 50px" src="{{ asset('images/error.png') }}" alt="fout">
                        <ul>
                            @if ($errors->has('error'))
                                <li>{{ $errors->first('error') }}</li>
                            @else
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            @endif
                        </ul>
                        <a href="#" class="removeError" onclick="closeMessage(this)">Sluiten</a>
                    </div>
                @endif
            </div>
        @endif



        <footer> &copy Taxus software</footer>
    </div>
    
    <script>
        window.addEventListener('load', () => {
            const logo = document.getElementById('companyLogo');

            if (logo?.complete) {
                // Already loaded
                logo.classList.add('fadeUpEffect');
            } else if (logo) {
                // Wait until it's loaded
                logo.onload = () => {
                    logo.classList.add('fadeUpEffect');
                };
            }
        });
           function closeMessage(element) {
                const message = element.parentElement;
                message.classList.add('fade-out');
                setTimeout(() => {
                    message.style.display = 'none';
                }, 300);
            }

    </script>
    @auth
        <script>

            const toggle = () => {
                const element = document.getElementById('side-menu');
                const icon = document.getElementById('nav-icon4');
                element.classList.toggle('slide-in');
                icon.classList.toggle('open');
            };
            window.toggle = toggle;

            document.addEventListener('click', (event) => {
                const menu = document.getElementById('side-menu');
                const icon = document.getElementById('nav-icon4');
                const isMenuOpen = menu.classList.contains('slide-in');
                const clickedInsideMenu = menu.contains(event.target);
                const clickedToggle = event.target.closest('#nav-icon4');

                if (isMenuOpen && !clickedInsideMenu && !clickedToggle) {
                    menu.classList.remove('slide-in');
                    icon.classList.remove('open');
                }
            });

     

            const openConfirmationModal = (message, actionUrl, form = null) => {
                const modal = document.getElementById('confirmationModal');
                const modalText = document.getElementById('modalText');
                const confirmBtn = document.getElementById('confirmButton');

                if (!modal || !modalText || !confirmBtn) {
                    console.error('Modal elements missing:', {
                        modal,
                        modalText,
                        confirmBtn
                    });
                    return;
                }

                modalText.innerText = message;
                confirmBtn.dataset.url = actionUrl;

                if (form) {
                    if (!form.id) {
                        form.id = 'tempForm-' + Date.now();
                    }
                    confirmBtn.dataset.form = form.id;
                } else {
                    confirmBtn.dataset.form = '';
                }

                confirmBtn.disabled = false;
                modal.style.display = 'grid';
                confirmBtn.focus();
            };

            const closeModal = () => {
                const modal = document.getElementById('confirmationModal');
                if (modal) {
                    modal.style.display = 'none';
                }
            };

            const confirmAction = async () => {
                const confirmBtn = document.getElementById('confirmButton');
                if (!confirmBtn) {
                    console.error('Confirm button not found');
                    alert('Confirmation button not found. Please try again.');
                    return;
                }

                const formId = confirmBtn.dataset.form;
                const actionUrl = confirmBtn.dataset.url;

                confirmBtn.disabled = true;
                confirmBtn.innerText = 'Processing...';

                async function tryConfirm(csrfToken) {
                    return await fetch('/confirm-action', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            action: actionUrl,
                        }),
                    });
                }

                try {
                    const tokenResponse = await fetch('/refresh-csrf', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        },
                    });
                    if (!tokenResponse.ok) throw new Error(`Failed to fetch CSRF token: ${tokenResponse.status}`);
                    const tokenData = await tokenResponse.json();
                    csrfToken = tokenData.csrfToken;
                    // console.log('CSRF token fetched:', csrfToken);

                    let confirmResponse = await tryConfirm(csrfToken);

                    if (confirmResponse.status === 419) {
                        console.warn('CSRF token expired, retrying...');
                        const retryTokenResponse = await fetch('/refresh-csrf', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                            },
                        });
                        if (!retryTokenResponse.ok) throw new Error(
                            `Failed to fetch CSRF token on retry: ${retryTokenResponse.status}`);
                        const retryTokenData = await retryTokenResponse.json();
                        csrfToken = retryTokenData.csrfToken;
                        // console.log('CSRF token retried:', csrfToken);
                        confirmResponse = await tryConfirm(csrfToken);
                    }

                    if (!confirmResponse.ok) {
                        throw new Error(`Confirm action failed: ${confirmResponse.status}`);
                    }

                    const data = await confirmResponse.json();
                    if (data.success) {
                        if (formId) {
                            const formToSubmit = document.getElementById(formId);
                            if (formToSubmit) {
                                // console.log('Submitting form:', formId);
                                formToSubmit.submit();
                            } else {
                                console.error('Form not found:', formId);
                                throw new Error('Form submission failed');
                            }
                        } else {
                            // console.log('Redirecting to:', actionUrl);
                            window.location.href = actionUrl;
                        }
                        closeModal();
                    } else {
                        throw new Error('Confirmation not successful');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(`Action failed: ${error.message}`);
                    confirmBtn.disabled = false;
                    confirmBtn.innerText = 'Confirm';
                }
            };

            document.addEventListener('visibilitychange', async () => {
                if (document.visibilityState === 'visible') {
                    try {
                        const response = await fetch('/refresh-csrf', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                            },
                        });
                        if (!response.ok) throw new Error('Failed to fetch CSRF token');
                        const data = await response.json();
                        csrfToken = data.csrfToken;
                        // console.log('CSRF token refreshed on visibility:', csrfToken);
                    } catch (error) {
                        console.error('Failed to refresh CSRF token on visibility:', error);
                    }
                }
            });

            const confirmBtn = document.getElementById('confirmButton');
            if (confirmBtn) {
                confirmBtn.addEventListener('touchend', (e) => {
                    e.preventDefault();
                    confirmAction();
                });
            }

            window.onclick = function(event) {
                const modal = document.getElementById('confirmationModal');
                if (event.target === modal) {
                    closeModal();
                }
            };

            window.openConfirmationModal = openConfirmationModal;
            window.closeModal = closeModal;
            window.confirmAction = confirmAction;

        </script>
    @endauth


    <div id="confirmationModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <p id="modalText"></p>
            <button id="confirmButton" class="modal-button" onclick="confirmAction()">Confirm</button>
            <button class="modal-button cancel" onclick="closeModal()">Cancel</button>
        </div>
    </div>
</body>

</html>
