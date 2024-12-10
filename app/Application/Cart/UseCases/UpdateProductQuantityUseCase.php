<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Exceptions\InsufficientStockException;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class UpdateProductQuantityUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId, string $productId, int $newQuantity): void
    {
        $product = $this->productRepository->findByIdOrFail(UUidVO::fromString($productId));
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));

        if ($product->quantity < $newQuantity) {
            throw new InsufficientStockException($productId);
        }

        $cart->updateProductQuantity($productId, $newQuantity);
        $this->cartRepository->save($cart);
        $this->productRepository->updateStock($product->id, $newQuantity);
    }
}
