@extends('layouts.app')

@section('content')
    <h1>{{ __('cart.your_cart') }}</h1>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>{{ __('cart.product_name') }}</th>
            <th>{{ __('cart.quantity') }}</th>
            <th>{{ __('cart.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($cart->items as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>
                    <form action="{{ route('cart.updateProduct', ['cartId' => $cart->id, 'productId' => $item->product->id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control d-inline w-25">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('cart.update') }}</button>
                    </form>
                </td>
                <td>
                    <form action="{{ route('cart.removeProduct', ['cartId' => $cart->id, 'productId' => $item->product->id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">{{ __('cart.remove') }}</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <form action="{{ route('cart.checkout', ['cartId' => $cart->id]) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success">{{ __('cart.checkout') }}</button>
    </form>
@endsection
