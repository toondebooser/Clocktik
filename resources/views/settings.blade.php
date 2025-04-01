@extends('layout')

@section('content')
    <style>
        .checkboxContainer .radioBox:checked~.checkMark {
            background-color: {{ $data->color }};
        }

        .checkboxContainer {
            width: 100%;
            height: fit-content;
        }

        .labelAndere {
            font-size: 15px;

        }

        .container-drag-drop {
            display: flex;
            justify-content: center;
            grid-column: 1/12;
            grid-row: 5/6;
            align-self: end;
        }

        .flex {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .side {
            /* max-height: 200px; */
            border-radius: 20px;
            width: 150px;
            height: inherit;
            margin-right: 5px;
            padding: 10px;
            background: #f9f9f9;
            border: 2px solid {{ $data->color }};
        }
        .name {
            border-radius: 10px;
            padding: 10px;
            margin: 5px;
            background: #ddd;
            cursor: move;
        }

        .side.dragover {
            background: #e0e0e0;
            border-color: #000;

        }

        .content {
            overflow-x: scroll;
        }
    </style>
    <h2>Instellingen</h2>
    <div style=" max-width: 400px; height: 100%;justify-content:center; align-content:center; grid-row: 3/5; grid-column: 2/12; justify-self:center;"
        class="content">
        <form
            style=" margin: 10px 0px; grid-gap:10px; border-radius: 20px; padding: 10px;border: solid black 1px;display:grid; grid-template-rows:repeat(5, auto); grid-template-columns:repeat(2,auto); "
            action="{{ route('change-company-settings') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input style="display: none" id="colorInput" type="color" name="companyColor" value="{{ $data->color }}">
            <label class="flex" for="colorInput">
                <div>Kies een kleur</div>
                <div id="colorPreview"
                    style=" border-radius: 10px; width: 50px; height: 50px;background-color: {{ $data->color }}"></div>
            </label>
            <input style="display: none" type="file" name="picture" id="pictureInput" accept="image/*">
            <label for="pictureInput" class="logo-selectie flex"
                style="grid-column: 2/3; width: 100px justify-self: center;">
                <div>Kies je logo</div>

                <img id="preview" for="file-input"
                    src="{{ asset($data->image) ?? asset('images/Taxus logo perfect round.png') }}" alt="Logo preview"
                    style="  grid-row: 2/3; grid-column: 2/3;justify-self: center;max-height: 50px; max-width: auto;">
            </label>

            <span style="grid-row: 2/3; grid-column: 1/3; justify-self: center" class="radioInput">
                <label for="betaaldInput" class="checkboxContainer">
                    <input @if ($data->Admin_timeclock == 1) {{ 'checked' }} @endif type="checkbox" class="radioBox" id="betaaldInput" name="adminTimeClock">
                    <span class="checkMark"></span>
                    <span class="labelAndere">Admin tijdregistratie activeren</span>
                </label>
            </span>
            <span style="grid-column: 1/3; grid-row:3/4; justify-self:center;">
                <label for="werkUren">Daguren (decimaal)</label>
                <input value="{{$data->day_hours}}" style="width:60px;" type="number" name="werkUren" id="werkUren">
            </span>
            <div style="justify-self: end">Weekend dagen</div>
            <span>
                <select style="width: fit-content" name="weekendDag1" id="weekendDag1">
                    <option value="Maandag" {{  $data->weekend_day_1 == 'Maandag' ? 'selected' : '' }}>Ma</option>
                    <option value="Dinsdag" {{  $data->weekend_day_1 == 'Dinsdag' ? 'selected' : '' }}>Di</option>
                    <option value="Woensdag" {{  $data->weekend_day_1 == 'Woensdag' ? 'selected' : '' }}>Wo</option>
                    <option value="Donderdag" {{  $data->weekend_day_1 == 'Donderdag' ? 'selected' : '' }}>Do</option>
                    <option value="Vrijdag"{{  $data->weekend_day_1 == 'Vrijdag' ? 'selected' : '' }}>Vr</option>
                    <option value="Zaterdag"{{  $data->weekend_day_1 == 'Zaterdag' ? 'selected' : '' }}>Za</option>
                    <option value="Zondag"{{  $data->weekend_day_1 == 'Zondag' ? 'selected' : '' }}>Zo</option>
                </select>
                <select style="width: fit-content" name="weekendDag2" id="weekendDag2">
                    <option value="Maandag" {{  $data->weekend_day_2 == 'Maandag' ? 'selected' : '' }}>Ma</option>
                    <option value="Dinsdag" {{  $data->weekend_day_2 == 'Dinsdag' ? 'selected' : '' }}>Di</option>
                    <option value="Woensdag" {{  $data->weekend_day_2 == 'Woensdag' ? 'selected' : '' }}>Wo</option>
                    <option value="Donderdag" {{  $data->weekend_day_2 == 'Donderdag' ? 'selected' : '' }}>Do</option>
                    <option value="Vrijdag"{{  $data->weekend_day_2 == 'Vrijdag' ? 'selected' : '' }}>Vr</option>
                    <option value="Zaterdag"{{  $data->weekend_day_2 == 'Zaterdag' ? 'selected' : '' }}>Za</option>
                    <option value="Zondag"{{  $data->weekend_day_2 == 'Zondag' ? 'selected' : '' }}>Zo</option>
                </select>
            </span>





            <button class="button" type="submit"
                style="  grid-row: 5/6; grid-column: 1/3; justify-self: center; height: 30px">Update instellingen</button>
        </form>
        <div style="text-align: center"> &#8592; Sleep &#8594; <br> om van rechten te veranderen.</div>
        <div class="container-drag-drop">
            <div class="side" id="left" ondrop="drop(event)" ondragover="allowDrop(event)">
                <div>Admins</div>
                @foreach ($admins as $admin)
                    <div class="name" draggable="true" ondragstart="drag(event)" data-name="{{ $admin->name }}"
                        data-id="{{ $admin->id }}" data-company_code="{{ $admin->company_code }}">{{ $admin->name }}
                    </div>
                @endforeach

            </div>
            <div class="side" id="right" ondrop="drop(event)" ondragover="allowDrop(event)">
                <div>Arbeiders</div>
                @foreach ($workers as $worker)
                    <div class="name" draggable="true" ondragstart="drag(event)" data-id="{{ $worker->id }}"
                        data-company_code="{{ $worker->company_code }}" data-name="{{ $worker->name }}">
                        {{ $worker->name }}
                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <script>
        // Desktop Drag and Drop
        function allowDrop(event) {
            event.preventDefault();
            event.currentTarget.classList.add('dragover');
        }

        function drag(event) {
            event.dataTransfer.setData('text', event.target.dataset.name);
        }

        function drop(event) {
            event.preventDefault();
            const name = event.dataTransfer.getData('text');
            handleDrop(event.currentTarget, name);
        }

        // Touch Support
        const names = document.querySelectorAll('.name');
        const sides = document.querySelectorAll('.side');

        names.forEach(name => {
            name.addEventListener('dragstart', drag);
            name.addEventListener('touchstart', touchStart, {
                passive: false
            });
            name.addEventListener('touchmove', touchMove, {
                passive: false
            });
            name.addEventListener('touchend', touchEnd, {
                passive: false
            });
        });

        sides.forEach(side => {
            side.addEventListener('dragover', allowDrop);
            side.addEventListener('dragleave', () => side.classList.remove('dragover'));
            side.addEventListener('drop', drop);
        });

        let draggedElement = null;
        let offsetX = 0;
        let offsetY = 0;
        let originalParent = null; // Store the original parent

        function touchStart(event) {
            event.preventDefault();
            draggedElement = event.target;
            originalParent = draggedElement.parentElement; // Save original parent
            const touch = event.touches[0];
            offsetX = touch.clientX - draggedElement.getBoundingClientRect().left;
            offsetY = touch.clientY - draggedElement.getBoundingClientRect().top;
            draggedElement.style.position = 'absolute';
            draggedElement.style.zIndex = 1000;
            document.body.appendChild(draggedElement); // Bring to top
        }

        function touchMove(event) {
            event.preventDefault();
            if (!draggedElement) return;
            const touch = event.touches[0];
            draggedElement.style.left = `${touch.clientX - offsetX}px`;
            draggedElement.style.top = `${touch.clientY - offsetY}px`;

            // Highlight drop target
            sides.forEach(side => {
                const rect = side.getBoundingClientRect();
                if (touch.clientX >= rect.left && touch.clientX <= rect.right &&
                    touch.clientY >= rect.top && touch.clientY <= rect.bottom) {
                    side.classList.add('dragover');
                } else {
                    side.classList.remove('dragover');
                }
            });
        }

        function touchEnd(event) {
            event.preventDefault();
            if (!draggedElement || !originalParent) return;
            const touch = event.changedTouches[0];
            const name = draggedElement.dataset.name;

            let dropped = false;
            sides.forEach(side => {
                const rect = side.getBoundingClientRect();
                if (touch.clientX >= rect.left && touch.clientX <= rect.right &&
                    touch.clientY >= rect.top && touch.clientY <= rect.bottom) {
                    handleDrop(side, name);
                    dropped = true;
                }
            });

            // If not dropped on a side, restore to original parent
            if (!dropped) {
                originalParent.appendChild(draggedElement);
            }

            // Reset styles
            draggedElement.style.position = '';
            draggedElement.style.left = '';
            draggedElement.style.top = '';
            draggedElement.style.zIndex = '';
            draggedElement = null;
            originalParent = null; // Clear reference
            sides.forEach(side => side.classList.remove('dragover'));
        }

        function handleDrop(target, name) {
            const draggedElement = document.querySelector(`[data-name="${name}"]`);
            const userId = draggedElement.dataset.id;
            const companyCode = draggedElement.dataset.company_code;
            const sourceSide = draggedElement.closest('.side')?.id || 'unknown';
            const targetSide = target.id;

            target.appendChild(draggedElement);
            console.log('Dropping:', {
                name,
                userId,
                companyCode,
                sourceSide,
                targetSide
            });

            const url = '{{ route('changeAdminRights', ['id' => ':id', 'company_code' => ':company_code']) }}'
                .replace(':id', userId)
                .replace(':company_code', companyCode);

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.style.display = 'none';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            const targetSideInput = document.createElement('input');
            targetSideInput.type = 'hidden';
            targetSideInput.name = 'target_side';
            targetSideInput.value = targetSide;
            form.appendChild(targetSideInput);

            document.body.appendChild(form);
            form.submit();
        }

        // Prevent default drop behavior outside sides (for desktop)
        document.addEventListener('dragover', (event) => {
            if (!event.target.closest('.side')) {
                event.preventDefault(); // Prevent drop outside sides
            }
        });

        document.addEventListener('drop', (event) => {
            if (!event.target.closest('.side')) {
                event.preventDefault();
                const name = event.dataTransfer.getData('text');
                const draggedElement = document.querySelector(`[data-name="${name}"]`);
                if (draggedElement && originalParent) {
                    originalParent.appendChild(draggedElement); // Restore on invalid drop
                }
            }
        });
        // Get the input and img elements
        const imageInput = document.getElementById('pictureInput');
        const colorInput = document.getElementById('colorInput')
        const colorPreview = document.getElementById('colorPreview')
        const preview = document.getElementById('preview');

        // Listen for file selection
        imageInput.addEventListener('input', function() {
            const file = this.files[0];

            if (file) {
                if (file.type.startsWith('image/')) {
                    const imageUrl = URL.createObjectURL(file);
                    preview.src = imageUrl;
                } else {
                    alert('Please select an image file.');
                    preview.src = '';
                    preview.style.display = 'none';
                }
            }
        });
        colorInput.addEventListener('input', function() {
            const color = this.value;
            if (color) {
                colorPreview.style.backgroundColor = color;

            }
        })
        colorInput.addEventListener('change', function() {
            this.blur(); // Close the picker after final selection
        });
    </script>
@endsection
