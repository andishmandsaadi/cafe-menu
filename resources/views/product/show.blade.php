@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>{{ $owner->first_name }} Kafe - {{ $category->name }} - {{ $product->name }}</h2>

    <div class="row mt-4">
        <div class="col-md-6">
            @if ($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" alt="{{ $product->name }}" style="max-height: 400px; object-fit: cover;">
            @endif
        </div>
        <div class="col-md-6">
            <h3>{{ $product->name }}</h3>
            <p class="text-muted">{{ $product->description }}</p>
            <h4 class="fw-bold">Fiyat: {{ $product->owner_price }} TL</h4>
        </div>
    </div>

    <!-- Alternative Cafes -->
    @if ($alternativeCafes->isNotEmpty())
        <div class="mt-5">
            <h4>Alternatif Kafeler</h4>
            <ul class="list-group">
                @foreach ($alternativeCafes as $altCafe)
                    @php
                        // Get the product price in the alternative cafe
                        $altProduct = $altCafe->products->first();
                        $altPrice = $altProduct->pivot->price ?? 'N/A';
                    @endphp
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $altCafe->first_name }}'s Cafe</strong> - {{ $altPrice }} TL
                        </div>
                        <a href="{{ route('product.show', [$altCafe->username, $category->slug, $product->slug]) }}" class="btn btn-primary btn-sm">
                            Ürünü Gör
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
