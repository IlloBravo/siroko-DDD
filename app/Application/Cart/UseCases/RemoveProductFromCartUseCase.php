<?php

namespace App\Application\Cart\UseCases;

use App\Domain\Cart\Exceptions\CartNotFoundException;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Domain\Shared\ValueObjects\UuidVO;

readonly class RemoveProductFromCartUseCase
{
    public function __construct(
        private CartRepositoryInterface $cartRepository,
        private ProductRepositoryInterface $productRepository
    ) {}

    /**
     * @throws CartNotFoundException
     */
    public function execute(string $cartId, string $productId): void
    {
        $uuidProduct = UuidVO::fromString($productId);
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));

        $quantityRemoved = $cart->getProductQuantity($uuidProduct);
        $cart->removeProduct($uuidProduct);
        $this->cartRepository->save($cart);

        $this->productRepository->increaseStock($uuidProduct, $quantityRemoved);
    }
}
