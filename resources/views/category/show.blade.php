@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>{{ $owner->first_name }} Kafenin {{ $category->name }}</h2>

    <!-- Display category image -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                @if ($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" class="card-img-top img-fluid" style="height: 250px; object-fit: cover;" alt="{{ $category->name }}">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $category->name }}</h5>
                    <p class="card-text">Bu kategorideki lezzetli ürünlerimizi keşfedin.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Display products in this category assigned to the cafe owner -->
    <div class="row mt-4">
        <h3>Ürünler</h3>
        @if ($products->count() > 0)
            @foreach ($products as $product)
                <div class="col-md-4">
                    <div class="card">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top img-fluid" style="height: 200px; object-fit: cover;" alt="{{ $product->name }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('product.show', [$owner->username, $category->slug, $product->slug]) }}">
                                    {{ $product->name }}
                                </a>
                            </h5>
                            <p class="card-text">{{ $product->description }}</p>
                            <p class="fw-bold">Fiyat: {{ $product->owner_price }} TL</p>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted">Bu kafeye ait bu kategoride ürün mevcut değil.</p>
        @endif
    </div>
</div>
@endsection
