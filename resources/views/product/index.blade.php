@extends('layouts.app')

@section('content')
    <h1>{{ __('Cart.available_products') }}</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
        <tr>
            <th>{{ __('Cart.product_name') }}</th>
            <th>{{ __('Cart.price') }}</th>
            <th>{{ __('Cart.stock') }}</th>
            <th>{{ __('Cart.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->quantity }}</td>
                <td>
                    <form action="{{ route('api.cart.createCart') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="number" name="quantity" min="1" max="{{ $product->quantity }}" class="form-control d-inline w-25" required>
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Cart.add_to_cart') }}</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
