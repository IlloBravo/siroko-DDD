@extends('layouts.app')

@section('content')
    <div class="text-center mt-5">
        <h1>{{ __('Cart.thank_you') }}</h1>
        <p>{{ __('Cart.order_success') }}</p>
        <a href="{{ route('products.index') }}" class="btn btn-primary">{{ __('Cart.continue_shopping') }}</a>
    </div>
@endsection
