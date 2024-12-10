@extends('layouts.app')

@section('content')
    <div class="text-center mt-5">
        <h1>{{ __('cart.thank_you') }}</h1>
        <p>{{ __('cart.order_success') }}</p>
        <a href="{{ route('cart.index') }}" class="btn btn-primary">{{ __('cart.continue_shopping') }}</a>
    </div>
@endsection
