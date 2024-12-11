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
     * @throws InsufficientStockException
     */
    public function execute(string $cartId, string $productId, int $newQuantity): void
    {
        $uuidProduct = UuidVO::fromString($productId);
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));

        $cart->updateProductQuantity($uuidProduct, $newQuantity);

        $this->cartRepository->save($cart);
        $this->productRepository->save($cart->getProduct($uuidProduct));
    }
}
