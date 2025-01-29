@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>{{ $owner->first_name }} Kafenin Menüsü</h2>
    <div class="row mt-4">
        @foreach ($owner->categories as $category)
            <div class="col-md-4">
                <div class="card">
                    <img src="{{ asset('storage/'.$category->image) }}" class="card-img-top category-img" alt="{{ $category->name }}">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('category.show', [$owner->username, $category->slug]) }}">
                                {{ $category->name }}
                            </a>
                        </h5>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .category-img {
        width: 100%; /* Make it responsive */
        height: 200px; /* Fixed height */
        object-fit: cover; /* Crop and fit the image */
        border-radius: 10px; /* Rounded corners */
    }
</style>
@endsection
