@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Hoşgeldiniz! Bir kafe seçiniz:</h2>
    <div class="list-group">
        @foreach ($owners as $owner)
            <a href="{{ route('cafe.show', $owner->username) }}" class="list-group-item list-group-item-action">
                {{ $owner->first_name }} {{ $owner->last_name }}
            </a>
        @endforeach
    </div>
</div>
@endsection
