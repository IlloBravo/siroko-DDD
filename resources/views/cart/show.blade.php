@extends('layouts.app')

@section('content')
    <h1>{{ __('Cart.your_cart') }}</h1>

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
                    <form action="{{ url('api/cart/' . $cart->id . '/products/' . $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="number" name="quantity" value="{{ $product->quantity }}" min="1" class="form-control d-inline w-25">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Cart.update') }}</button>
                    </form>
                </td>
                <td>
                    <form action="{{ url('api/cart/' . $cart->id . '/products/' . $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">{{ __('Cart.remove') }}</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <form action="{{ url('api/cart/' . $cart->id . '/checkout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success">{{ __('Cart.checkout') }}</button>
    </form>
@endsection
