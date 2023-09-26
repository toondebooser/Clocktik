@extends('layout')
@section('content')
@foreach ($workers as $worker )
{{-- @if (!$worker->admin) --}}
<form action="{{route('getData')}}" method="post">
@csrf
<button class='workerButton' type="submit" name='worker' value="{{$worker->id}}">
    {{$worker->name;}}
</button>
    
</form>
{{-- @endif --}}
@endforeach
@endsection