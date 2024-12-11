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
        $product = $this->productRepository->findByIdOrFail($uuidProduct);
        $cart = $this->cartRepository->findByIdOrFail(UuidVO::fromString($cartId));

        $currentQuantity = $cart->getProductStock($uuidProduct);

        $difference = $newQuantity - $currentQuantity;

        if ($difference > 0 && $product->quantity < $difference) {
            throw new InsufficientStockException($productId);
        }

        $cart->updateProductQuantity($uuidProduct, $newQuantity);

        if ($difference > 0) {
            $this->productRepository->updateStock($uuidProduct, $difference);
        } elseif ($difference < 0) {
            $this->productRepository->increaseStock($uuidProduct, abs($difference));
        }

        $this->cartRepository->save($cart);
    }
}
