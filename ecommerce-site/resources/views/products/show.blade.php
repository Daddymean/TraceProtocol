@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $product->name }}</div>

                <div class="card-body">
                    <p>{{ $product->description }}</p>
                    <p>Price: ${{ $product->price }}</p>
                    <img src="/images/products/{{ $product->image }}" alt="{{ $product->name }}">

                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection