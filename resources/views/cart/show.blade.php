@extends('layouts.app')

@section('content')
    <h1>{{ __('Cart.your_cart') }}</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('api.cart.updateProduct', ['cartId' => $cart->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <table class="table table-striped">
            <thead>
            <tr>
                <th>{{ __('Cart.product_name') }}</th>
                <th>{{ __('Cart.quantity') }}</th>
                <th>{{ __('Cart.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($cart->items as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>
                        <input type="number" name="products[{{ $product->id }}][quantity]" value="{{ $product->quantity }}" min="1" class="form-control w-50">
                    </td>
                    <td>
                        <form action="{{ route('api.cart.removeProduct', ['cartId' => $cart->id, 'productId' => $product->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                {{ __('Cart.remove') }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">{{ __('Cart.update') }}</button>
        <a href="{{ url('api/cart/' . $cart->id . '/checkout') }}" class="btn btn-success">{{ __('Cart.checkout') }}</a>
    </form>
@endsection
