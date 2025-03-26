@extends('layout')
@section('content')
<style>
    .container {
        display: flex;
        justify-content: space-around;
        margin: 20px;
    }
    .side {
        width: 200px;
        min-height: 200px;
        border: 2px solid #333;
        padding: 10px;
        background: #f9f9f9;
    }
    .name {
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
</head>
<body>
<div class="container">
    <div class="side" id="left" ondrop="drop(event)" ondragover="allowDrop(event)">
        <h3>Left Side</h3>
        @foreach ($admins as $admin)
            <div class="name" draggable="true" ondragstart="drag(event)" data-name="{{ $admin->name }}">{{ $admin->name }}</div>
        @endforeach
    </div>
    <div class="side" id="right" ondrop="drop(event)" ondragover="allowDrop(event)">
        <h3>Right Side</h3>
        @foreach ($workers as $worker)
            <div class="name" draggable="true" ondragstart="drag(event)" data-name="{{ $worker->name }}">{{ $worker->name }}</div>
        @endforeach
    </div>
</div>

    <div style="grid-row: 3/4; grid-column: 3/10" class="content">

        @foreach ($admins as $admin)
        <a  href="">
            {{$admin->name}}
        </a>
        @endforeach <br>


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
            name.addEventListener('touchstart', touchStart, { passive: false });
            name.addEventListener('touchmove', touchMove, { passive: false });
            name.addEventListener('touchend', touchEnd, { passive: false });
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

        // Shared drop handler
        function handleDrop(target, name) {
            const draggedElement = document.querySelector(`[data-name="${name}"]`);
            target.appendChild(draggedElement);

            // Activate the route
            fetch(`/move-name/${name}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                console.log(data.message);
                alert(data.message); // Optional feedback
            })
            .catch(error => console.error('Error:', error));
        }
    </script>
@endsection
