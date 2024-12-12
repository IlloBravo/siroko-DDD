@extends('layouts.app')

@section('content')
    <h1>{{ __('Cart.available_products') }}</h1>

    <!-- Mensaje de éxito -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div id="alert-container"></div>

    <form id="add-to-cart-form" data-cart-id="{{ $cart->id }}">
        @csrf
        <table class="table table-striped">
            <thead>
            <tr>
                <th>{{ __('Cart.product_name') }}</th>
                <th>{{ __('Cart.price') }}</th>
                <th>{{ __('Cart.stock') }}</th>
                <th>{{ __('Cart.quantity') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->price }}</td>
                    <td>{{ $product->stock }}</td>
                    <td>
                        <input type="hidden" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}">
                        <input type="number" name="products[{{ $loop->index }}][quantity]" min="0" max="{{ $product->stock }}" class="form-control w-50" value="0">
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary mt-3">{{ __('Cart.add_to_cart') }}</button>
    </form>


    <div id="cart-button-container" class="mt-4" style="display: none;">
        <a id="view-cart-button" href="#" class="btn btn-success">{{ __('Cart.view_cart') }}</a>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#add-to-cart-form').on('submit', function (e) {
                e.preventDefault();

                const cartId = $(this).data('cart-id');

                const formData = $(this).serializeArray();

                const payload = {
                    cart_id: cartId,
                    products: []
                };

                let currentProduct = {};
                formData.forEach((field) => {
                    if (field.name.includes('[id]')) {
                        currentProduct = { id: field.value };
                    }
                    if (field.name.includes('[quantity]')) {
                        currentProduct.quantity = parseInt(field.value, 10);
                        payload.products.push(currentProduct);
                    }
                });

                $.ajax({
                    url: '{{ route('api.cart.addProduct', ['cartId' => ':cartId']) }}'.replace(':cartId', cartId),
                    method: 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    success: function (response) {
                        $('#alert-container').html('<div class="alert alert-success">' + response.message + '</div>');
                    },
                    error: function () {
                        $('#alert-container').html('<div class="alert alert-danger">{{ __('Cart.add_product_error') }}</div>');
                    }
                });
            });

        });
    </script>
@endsection
