@extends('layouts.app')

@section('content')
    <div class="text-center mt-5">
        <h1>{{ __('cart.thank_you') }}</h1>
        <p>{{ __('cart.order_success') }}</p>
        <a href="{{ route('cart.index') }}" class="btn btn-primary">{{ __('Cart.continue_shopping') }}</a>
    </div>

    <a href="{{ url('/products') }}" class="btn btn-secondary mt-3">{{ __('Cart.go_to_products') }}</a>

@endsection
