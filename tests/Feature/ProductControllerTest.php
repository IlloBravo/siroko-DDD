<?php

use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    public function test_product_index_shows_products()
    {
        // Seeders create 5 products
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);

        $response->assertViewIs('product.index');

        $response->assertViewHas('products', function ($products) {
            return count($products) === 5;
        });
    }
}
