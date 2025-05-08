@extends('layout')
@section('title')
    <h1 class="welcomeTitle fadeUpEffect" style="margin-top: 100px">Werkuren.be</h1>

    <a href="{{ route('pricing') }}" class="logoContainer" >
        <img id="companyLogo" class="logo " style="opacity: 0"
            src="{{ auth()->check() && auth()->user()->company && auth()->user()->company->company_logo ? asset(auth()->user()->company->company_logo) : asset('images/TaxusLogo.png') }}"
            alt="Company Logo">
    </a>
    <div class="welcomCard fadeUpEffect" >
        <p style="margin-bottom: 15px;">
            Registreer werktijden.<br>
            Snel & duidelijk.
        </p>
        <a href="{{ route('pricing') }}" class="button "
            style="grid-row:3/4; grid-column:1/13; justify-self:center; height:fit-content; align-self:end ">Start
            vandaag</a>
    </div>
@endsection
<script>
    window.addEventListener('load', () => {
        const logo = document.getElementById('companyLogo');

        if (logo.complete) {
            // Already loaded
            logo.classList.add('fadeUpEffect');
        } else {
            // Wait until it's loaded
            logo.onload = () => {
                logo.classList.add('fadeUpEffect');
            };
        }
    });
</script>