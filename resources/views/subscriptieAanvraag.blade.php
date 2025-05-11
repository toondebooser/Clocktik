@extends('layout')
@section('content')

<div class="subscriptieForm">
    <h2>Subscriptie aanvraag Werkuren.be</h2>
    <form method="POST" action="{{ route('subscribe.send') }}" class="p-4 bg-white shadow " style="border-radius: 15px; display:flex; flex-direction:column; gap:20px;" >
        @csrf

                <input type="text" name="name" class="form-control uniform-input" placeholder="Your name" required>
                <input type="text" name="company" class="form-control uniform-input" placeholder="Company name" required>
                <input type="email" name="email" class="form-control uniform-input" placeholder="Your email" required>
                <input type="text" name="btw" placeholder="Btw-nummer" required>
                <input type="text" name="adres" class="form-control uniform-input" placeholder="facturatie adres" required>
        <button type="submit" class="button">Subscribe</button>
    </form>
</div>
</html>
@endsection