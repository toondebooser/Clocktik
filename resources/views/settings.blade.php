@extends('layout')

@section('content')
    <style>
        .container-drag-drop {
            display: flex;
            justify-content: center;
            height: 200px;
            grid-column: 1/12;
            grid-row: 5/6;
            align-self: end;
        }

        .side {
            max-height: 200px;
            border-radius: 20px;
            overflow: auto;
            width: 150px;
            height: 100%;
            margin-right: 5px;
            padding: 10px;
            background: #f9f9f9;
            border: 2px solid {{$data->color}};
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
    </style>
    <h2>Instellingen</h2>
    <div style=" height: 100%;justify-content:center; align-content:center; grid-row: 3/4; grid-column: 2/12"
        class="content">
        <form style=" margin: 10px 0px; grid-gap:10px; border-radius: 20px; padding: 10px;border: solid black 1px;display:grid; grid-template-rows:repeat(3, auto); grid-template-columns:repeat(2,auto); " action="{{route('change-company-settings')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <input style="display: none" id="color-selection" type="color" name="companyColor" value="{{$data->color}}">
            <label for="color-selection">Kies een kleur
                <div style=" width: 50px; height: 50px;background-color: {{$data->color}}"></div>
            </label>
            <input style="display: none" type="file" id="file-input" name="picture" id="pictureInput" accept="image/*">
            <label for="file-input" class="logo-selectie" style="grid-column: 2/3; width: 100px">Kies je logo
            
            <img id="preview" for="file-input" src="{{asset($data->image)}}" alt="Logo preview" style="  grid-row: 2/3; grid-column: 2/3;justify-self: center;max-height: 50px; max-width: auto;">
            </label>


            <button class="button" type="submit" style="  grid-row: 3/4; grid-column: 1/3; justify-self: center; height: 30px">Update instellingen</button>
        </form>
<div style="text-align: center">  &#8592; Sleep &#8594; <br> om van rechten te veranderen.</div>
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
                        data-company_code="{{ $worker->company_code }}" data-name="{{ $worker->name }}">{{ $worker->name }}
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

        function touchStart(event) {
            event.preventDefault();
            draggedElement = event.target;
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
            if (!draggedElement) return;
            const touch = event.changedTouches[0];
            const name = draggedElement.dataset.name;

            sides.forEach(side => {
                const rect = side.getBoundingClientRect();
                if (touch.clientX >= rect.left && touch.clientX <= rect.right &&
                    touch.clientY >= rect.top && touch.clientY <= rect.bottom) {
                    handleDrop(side, name);
                }
            });

            // Reset styles
            draggedElement.style.position = '';
            draggedElement.style.left = '';
            draggedElement.style.top = '';
            draggedElement.style.zIndex = '';
            draggedElement = null;
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

            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            // Add targetSide
            const targetSideInput = document.createElement('input');
            targetSideInput.type = 'hidden';
            targetSideInput.name = 'target_side';
            targetSideInput.value = targetSide;
            form.appendChild(targetSideInput);

            document.body.appendChild(form);
            form.submit();
        }
         // Get the input and img elements
        const input = document.getElementById('pictureInput');
        const preview = document.getElementById('preview');

        // Listen for file selection
        input.addEventListener('change', function() {
            const file = this.files[0]; // Get the first selected file

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
    </script>
@endsection
