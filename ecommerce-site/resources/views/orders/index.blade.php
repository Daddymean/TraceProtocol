@extends('layouts.app')

@section('content')
<div class="container">
    <h2>My Orders</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->created_at }}</td>
                <td>{{ $order->total_price }}</td>
                <td>{{ $order->status }}</td>
                <td>
                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary">View Details</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection