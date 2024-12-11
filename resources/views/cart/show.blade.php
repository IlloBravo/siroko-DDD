@extends('layouts.app')

@section('content')
    <h1>{{ __('Cart.your_cart') }}</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('api.cart.updateProduct', ['cartId' => $cart->id]) }}" method="POST">
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
            @foreach ($cart->items as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>
                        <input type="number" name="products[{{ $product->id }}][quantity]" value="{{ $product->quantity }}" min="1" class="form-control w-50">
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm delete-button" data-delete-url="{{ route('api.cart.removeProduct', ['cartId' => $cart->id, 'productId' => $product->id]) }}">
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
                            alert('Producto eliminado exitosamente.');
                            location.reload();
                        },
                        error: function () {
                            alert('Error al eliminar el producto.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
