@extends('layout')

@section('content')
<style>
    #searchInput{

        }
        </style>

<h2>{{$type}}</h2>
<div id="list" class="workersForm">
        <input type="text" id="searchInput" placeholder="Type a worker name..."> <br>
        @foreach ($dataSet as $data)
        @if ($type !== 'Bedrijven' && !$data->admin || ($data->admin && $data->company->Admin_timeclock))
        <form class="workerForm" action="{{ $type === 'Personeel' ? route('getData') : route('specials') }}"
        method="post">
        @csrf
        <button class="workerButton" type="submit" name="worker" value="{{ $data->id }}">
            {{ $data->name . ($data->admin ? "  (admin)" : "") }}
            <div class="{{ $data->timelogs->ShiftStatus ? ($data->timelogs->BreakStatus ? 'onBreak' : 'working') : 'notWorking' }}"></div>
        </button>
    </form>
    
    @elseif ($type === 'Bedrijven')
    <div class="bedrijvenLijst">
        <form class="workerForm"
        action="{{ route('myList', ['type' => 'Personeel', 'company_code' => $data->company_code]) }}"
        method="get">
        @csrf
        <button class="workerButton" type="submit" name="worker"
        style="display: flex; align-items: center; justify-content: center;">
        {{ $data->company_name }}
                            <img style="height: 30px; margin-left: 10px;" src="{{ asset($data->image) }}" alt="Company logo">
                        </button>
                    </form>
                    <a href="{{route('adminSettings',['company_code' => $data->company_code])}}">
                        <img  style="height: 50px" src="{{asset("images/2849830-gear-interface-multimedia-options-setting-settings_107986.png")}}" alt="settings">
                    </a>
                </div> @if ($loop->last)
                    <a class="specialsButton" href="{{ route('addCompany') }}">Bedrijf toevoegen</a>
                    @endif
                    @endif
                    @if ($type === 'Personeel' && $loop->last)
                    <a href="{{ route('myList', ['type' => 'Voor wie', 'company_code' => $data->company_code]) }}"
                        class="specialsButton">Dagen instellen</a>
                        @endif
                        @endforeach
                        
        @if ($type === 'Voor wie?')
            <form class="workerForm" method="post" action="{{ route('specials') }}">
                @csrf
                <button class="workerButton" type="submit" name="worker" value="{{ $dataSet }}">Voor
                    iedereen</button>
            </form>
        @endif
    </div>
    <script>
        // JavaScript to filter the list
        const searchInput = document.getElementById('searchInput');
        const list = document.getElementById('list');
        const workerForms = Array.from(list.getElementsByClassName('workerForm'));

        searchInput.addEventListener('input', function() {
            const filter = searchInput.value.toLowerCase();

            // Use forEach to loop through worker forms
            workerForms.forEach(form => {
                const button = form.querySelector('.workerButton');
                const name = button.textContent.toLowerCase().trim(); // Get the button text (name + "(admin)" if present)

                // Show form if name matches the search, hide if it doesn't
                if (name.startsWith(filter)) {
                    form.classList.remove('hidden');
                } else {
                    form.classList.add('hidden');
                }
            });
        });
    </script>
    <!-- Dagen instellen link -->
@endsection
