@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Order Details</div>

                <div class="card-body">
                    <h5 class="card-title">Order ID: {{ $order->id }}</h5>
                    <p class="card-text">Order Date: {{ $order->created_at }}</p>
                    <p class="card-text">Total Price: ${{ $order->total_price }}</p>

                    <h5 class="mt-4">Products:</h5>
                    <ul>
                        @foreach($order->products as $product)
                            <li>{{ $product->name }} - ${{ $product->price }}</li>
                        @endforeach
                    </ul>

                    <a href="{{ route('orders.index') }}" class="btn btn-primary">Back to Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection