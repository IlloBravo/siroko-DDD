@extends('layouts.app')

@section('content')
    <div class="alert alert-success">
        <h2>{{ __('Cart.cart_created') }}</h2>
        <p>{{ __('Cart.cart_created_successfully', ['cartId' => $cartId]) }}</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">{{ __('Cart.continue_shopping') }}</a>
    </div>
@endsection
