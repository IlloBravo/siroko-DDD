@extends('layouts.app')

@section('content')
    <h1>{{ __('Cart.available_products') }}</h1>

    <!-- Mensaje general de éxito o error -->
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div id="alert-container"></div>

    <!-- Formulario para agregar productos al carrito -->
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
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td id="stock-{{ $product->id }}">{{ $product->stock }}</td>
                    <td>
                        <input type="hidden" name="products[{{ $loop->index }}][id]" value="{{ $product->id }}">
                        <input type="number"
                               name="products[{{ $loop->index }}][quantity]"
                               min="0" max="{{ $product->stock }}"
                               class="form-control product-quantity w-50"
                               value="0"
                               data-product-id="{{ $product->id }}">
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary mt-3">{{ __('Cart.add_to_cart') }}</button>
    </form>

    <!-- Botón para ver carrito (aparece tras éxito) -->
    <div id="cart-button-container" class="mt-4" style="display: none;">
        <a id="view-cart-button" href="{{ route('cart.show', ['cartId' => $cart->id]) }}" class="btn btn-success">{{ __('Cart.view_cart') }}</a>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Manejo del evento `submit` del formulario
            $('#add-to-cart-form').on('submit', function (e) {
                e.preventDefault(); // Evitamos que el formulario se envíe de forma tradicional

                const cartId = $(this).data('cart-id'); // ID del carrito
                const formData = $(this).serializeArray(); // Datos del formulario

                const payload = {
                    cart_id: cartId,
                    products: []
                };

                // Filtrar productos que NO tienen cantidad > 0
                let currentProduct = {};
                formData.forEach((field) => {
                    if (field.name.includes('[id]')) {
                        currentProduct = { id: field.value };
                    }

                    if (field.name.includes('[quantity]') && parseInt(field.value, 10) > 0) {
                        currentProduct.quantity = parseInt(field.value, 10);
                        payload.products.push(currentProduct);
                    }
                });

                // Validar si no se seleccionaron productos antes de enviar
                if (payload.products.length === 0) {
                    $('#alert-container').html('<div class="alert alert-warning">{{ __('Cart.no_products_selected') }}</div>');
                    return;
                }

                // Enviar la solicitud AJAX
                $.ajax({
                    url: '{{ route('api.cart.addProduct', ['cartId' => ':cartId']) }}'.replace(':cartId', cartId), // URL dinámica para el carrito
                    method: 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    success: function (response) {
                        // Mostrar mensaje de éxito
                        $('#alert-container').html('<div class="alert alert-success">' + response.message + '</div>');

                        // Actualizar stock visualmente tras éxito
                        payload.products.forEach((product) => {
                            const stockCell = $('#stock-' + product.id);
                            const currentStock = parseInt(stockCell.text(), 10);
                            stockCell.text(currentStock - product.quantity);
                        });

                        // Mostrar botón de ver carrito
                        $('#cart-button-container').show();
                    },
                    error: function (xhr) {
                        // Manejar errores específicos y mostrarlos
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errorsHtml = '<div class="alert alert-danger"><ul>';
                            Object.entries(xhr.responseJSON.errors).forEach(([key, error]) => {
                                errorsHtml += '<li>' + error + '</li>';
                            });
                            errorsHtml += '</ul></div>';
                            $('#alert-container').html(errorsHtml);
                        } else {
                            $('#alert-container').html('<div class="alert alert-danger">{{ __('Cart.add_product_error') }}</div>');
                        }
                    }
                });
            });
        });
    </script>
@endsection