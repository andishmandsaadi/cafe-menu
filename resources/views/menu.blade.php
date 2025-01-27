@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $cafe->username }} Menu</h1>
    @foreach($categories as $category)
        <h2>{{ $category->name }}</h2>
        @foreach($category->products as $product)
            <p>{{ $product->name }} - ${{ $product->price }}</p>
        @endforeach
    @endforeach
</div>
@endsection
