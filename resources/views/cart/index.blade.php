@extends('layouts.app')

@section('content')
    <h1>{{ __('Cart.available_products') }}</h1>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>{{ __('Cart.product_name') }}</th>
            <th>{{ __('Cart.price') }}</th>
            <th>{{ __('Cart.stock') }}</th>
            <th>{{ __('Cart.amount_to_build') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->quantity }}</td>
                <td>
                    <form action="{{ url('api/cart/' . $cartId . '/products') }}" method="POST">
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

    <h2 class="mt-5">{{ __('Cart.available_carts') }}</h2>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>{{ __('Cart.cart_id') }}</th>
            <th>{{ __('Cart.created_at') }}</th>
            <th>{{ __('Cart.updated_at') }}</th>
            <th>{{ __('Cart.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($carts as $cart)
            <tr>
                <td>{{ $cart->id }}</td>
                <td>{{ $cart->createdAt->format('Y-m-d') }}</td>
                <td>{{ $cart->updatedAt->format('Y-m-d') }}</td>
                <td>
                    <a href="{{ route('cart.show', ['cartId' => $cart->id]) }}" class="btn btn-success btn-sm">{{ __('Cart.view_cart') }}</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
