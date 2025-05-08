@extends('layout')
@section('content')

<div class="subscriptieForm">
    <h2>Subscriptie aanvraag Werkuren.be</h2>
    <form method="POST" action="{{ route('subscribe.send') }}" class="p-4 bg-white shadow " style="border-radius: 15px; display:flex; flex-direction:column;" >
        @csrf
        <div class="form-group">
            <input type="text" name="name" class="form-control uniform-input" placeholder="Your name..." required>
        </div>

        <div class="form-group">
            <input type="text" name="company" class="form-control uniform-input" placeholder="Company name..." required>
        </div>

        <div class="form-group">
            <input type="email" name="email" class="form-control uniform-input" placeholder="Your email..." required>
        </div>

        <button type="submit" class="button">Subscribe</button>
    </form>
</div>
</html>
@endsection