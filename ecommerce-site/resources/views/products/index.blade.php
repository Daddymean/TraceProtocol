@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Products</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @foreach($products as $product)
                        <div class="product">
                            <h2>{{ $product->name }}</h2>
                            <img src="/images/products/{{ $product->image }}" alt="{{ $product->name }}">
                            <p>{{ $product->description }}</p>
                            <p>Price: ${{ $product->price }}</p>
                            <a href="/products/{{ $product->id }}" class="btn btn-primary">View Product</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection