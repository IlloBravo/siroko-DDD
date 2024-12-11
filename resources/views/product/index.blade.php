@extends('layouts.app')

@section('content')
    <h1>{{ __('Cart.available_products') }}</h1>

    <!-- Mensaje de éxito -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div id="alert-container"></div>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>{{ __('Cart.product_name') }}</th>
            <th>{{ __('Cart.price') }}</th>
            <th>{{ __('Cart.stock') }}</th>
            <th>{{ __('Cart.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->quantity }}</td>
                <td>
                    <form class="add-to-cart-form">
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

    <div id="cart-button-container" class="mt-4" style="display: none;">
        <a id="view-cart-button" href="#" class="btn btn-success">{{ __('Cart.view_cart') }}</a>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.add-to-cart-form').on('submit', function (e) {
                e.preventDefault();
                let form = $(this);

                $.ajax({
                    url: '{{ route('api.cart.createCart') }}',
                    method: 'POST',
                    data: form.serialize(),
                    success: function (response) {
                        $('#alert-container').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#view-cart-button').attr('href', '/cart/' + response.cartId);
                        $('#cart-button-container').show();
                    },
                    error: function (xhr) {
                        $('#alert-container').html('<div class="alert alert-danger">{{ __('Cart.add_product_error') }}</div>');
                    }
                });
            });
        });
    </script>
@endsection
