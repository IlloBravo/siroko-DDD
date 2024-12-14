@extends('layouts.app')

@section('content')
    <h1>{{ __('Cart.your_cart') }}</h1>

    <div id="alert-container"></div>

    <form id="update-cart-form" action="{{ route('api.cart.updateCart', ['cartId' => $cart->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <table class="table table-striped">
            <thead>
            <tr>
                <th>{{ __('Cart.product_name') }}</th>
                <th>{{ __('Cart.quantity') }}</th>
                <th>{{ __('Cart.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($cart->cartItems as $cartItem)
                <tr>
                    <td>{{ $cartItem->product()->name }}</td>
                    <td>
                        <input type="number" name="products[{{ $cartItem->id }}][quantity]" value="{{ $cartItem->quantity }}" min="1" class="form-control w-50">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm delete-button" data-delete-url="{{ route('api.cart.removeCartItem', ['cartId' => $cart->id, 'cartItemId' => $cartItem->id]) }}">
                            {{ __('Cart.remove') }}
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">{{ __('Cart.update') }}</button>
    </form>

    <form action="{{ route('api.cart.checkout', ['cartId' => $cart->id]) }}" method="POST" class="mt-2">
        @csrf
        <button type="submit" class="btn btn-success">{{ __('Cart.checkout') }}</button>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.delete-button').on('click', function () {
                let deleteUrl = $(this).data('delete-url');

                if (confirm('¿Estás seguro de que quieres eliminar este producto del carrito?')) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'POST',
                        data: {
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            $('#alert-container').html('<div class="alert alert-success">' + response.message + '</div>');
                            setTimeout(() => {
                                $('#alert-container').fadeOut('slow', function () {
                                    location.reload();
                                });
                            }, 3000);
                        },
                        error: function (xhr) {
                            $('#alert-container').html('<div class="alert alert-danger">' + xhr.responseJSON.error + '</div>');
                            setTimeout(() => {
                                $('#alert-container').fadeOut('slow', function () {
                                    location.reload();
                            });
                            }, 3000);
                        }
                    });
                }
            });
            $('#update-cart-form').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#alert-container').html('<div class="alert alert-success">' + response.message + '</div>');
                        setTimeout(() => {
                            $('#alert-container').fadeOut('slow');
                        }, 3000);
                    },
                    error: function (xhr) {
                        $('#alert-container').html('<div class="alert alert-danger">' + xhr.responseJSON.error + '</div>');
                        setTimeout(() => {
                            $('#alert-container').fadeOut('slow', function () {
                                location.reload();
                        });
                        }, 3000);
                    }
                });
            });
        });
    </script>
@endsection
