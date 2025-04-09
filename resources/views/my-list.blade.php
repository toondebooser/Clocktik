@extends('layout')

@section('content')
    <style>
        #searchInput {}
    </style>

    <h2>{{ $type }}</h2>
    <div id="list" class="workersForm">
        <input type="text" id="searchInput" placeholder="Type a worker name..."> <br>
        <div class="hidden" id="type" type="{{$type}}"></div>
        @foreach ($dataSet as $data)
            @if (($type !== 'Bedrijven' && !$data->admin) || ($data->admin && $data->company->admin_timeclock))
                <form class="workerForm" 
                    action="{{ $type === 'Personeel' ? route('getData') : route('specials') }}" method="post">
                    @csrf
                    <button class="listButton" type="submit" name="worker" value="{{ $data->id }}">
                        {{ $data->name . ($data->admin ? '  (admin)' : '') }}
                        <div
                            class="{{ $data->timelogs->ShiftStatus ? ($data->timelogs->BreakStatus ? 'onBreak' : 'working') : 'notWorking' }}">
                        </div>
                    </button>
                </form>
            @elseif ($type == 'Bedrijven')
                <div class="bedrijvenLijst">
                    <form class="workerForm" companyCode="{{ $data->company_code }}"
                        action="{{ route('myList', ['type' => 'Personeel', 'company_code' => $data->company_code]) }}"
                        method="get">
                        @csrf
                        <button class="listButton" type="submit" name="worker"
                            style="display: flex; align-items: center; justify-content: center;">
                            {{ $data->company_name }}
                            <img style="height: 30px; margin-left: 10px;" src="{{ asset($data->company_logo) }}"
                                alt="Company logo">
                        </button>
                    </form>
                    <a id="{{$data->company_code}}"
                        href="{{ route('adminSettings', ['company_code' => $data->company_code]) }}">
                        <img style="height: 50px"
                            src="{{ asset('images/2849830-gear-interface-multimedia-options-setting-settings_107986.png') }}"
                            alt="settings">
                    </a>
                </div>
                @if ($loop->last)
                    <a class="specialsButton" href="{{ route('addCompany') }}">Bedrijf toevoegen</a>
                @endif
            @endif
            @if ($type == 'Personeel' && $loop->last)
                <a href="{{ route('myList', ['type' => 'Voor wie', 'company_code' => $data->company_code]) }}"
                    class="specialsButton">Dagen instellen</a>
            @endif
        @endforeach

        @if ($type === 'Voor wie?')
            <form class="workerForm" method="post" action="{{ route('specials') }}">
                @csrf
                <button class="listButton" type="submit" name="worker" value="{{ $dataSet }}">Voor
                    iedereen</button>
            </form>
        @endif
    </div>
    <script>
        // List filter
        const searchInput = document.getElementById('searchInput');
        const list = document.getElementById('list');
        const type = document.getElementById('type').getAttribute('type');
        const workerForms = Array.from(list.getElementsByClassName('workerForm'));
        
        searchInput.addEventListener('input', function() {
            const filter = searchInput.value.toLowerCase();
            
            // Loop trhough forms
            workerForms.map(form => {
                const companyCode = form.getAttribute('companyCode');
                const button = form.querySelector('.listButton');
                const settingsButton = companyCode ? document.getElementById(companyCode) : null;
                const name = button.textContent.toLowerCase()
            .trim(); // Get the button text (name + "(admin)" if present)
                console.log(settingsButton);
                // Show form if name matches the search, hide if it doesn't
                if (name.startsWith(filter)) {
                    form.classList.remove('hidden');
                    type == 'Bedrijven' ? settingsButton.classList.remove('hidden') : null;
                } else {
                    type == 'Bedrijven' ? settingsButton.classList.add('hidden') : null;
                    form.classList.add('hidden');
                }
            });
        });
    </script>
    <!-- Dagen instellen link -->
@endsection
