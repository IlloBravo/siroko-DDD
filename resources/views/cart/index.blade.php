@extends('layouts.app')

@section('content')
    <h1>{{ __('Cart.available_carts') }}</h1>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>{{ __('Cart.cart_id') }}</th>
            <th>{{ __('Cart.products') }}</th>
            <th>{{ __('Cart.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($carts as $cart)
            <tr>
                <td>{{ $cart->id }}</td>
                <td>
                    <ul>
                        @foreach ($cart->cartItems as $product)
                            <li>
                                <strong>{{ $product->name }}</strong> - {{ __('Cart.price') }}: {{ $product->price }} € - {{ __('Cart.quantity') }}: {{ $product->cartQuantity }}
                            </li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    <a href="{{ route('cart.show', ['cartId' => $cart->id]) }}" class="btn btn-success btn-sm">{{ __('Cart.view_cart') }}</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
