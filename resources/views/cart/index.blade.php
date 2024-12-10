@extends('layouts.app')

@section('content')
    <h1>{{ __('cart.available_products') }}</h1>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>{{ __('cart.product_name') }}</th>
            <th>{{ __('cart.price') }}</th>
            <th>{{ __('cart.stock') }}</th>
            <th>{{ __('cart.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->quantity }}</td>
                <td>
                    <form action="{{ route('cart.addProduct', ['cartId' => $cartId]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <input type="number" name="quantity" min="1" max="{{ $product->quantity }}" class="form-control d-inline w-25" required>
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('cart.add_to_cart') }}</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
