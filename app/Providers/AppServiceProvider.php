<?php

namespace App\Providers;

use App\Domain\Cart\Repository\CartItemRepositoryInterface;
use App\Infrastructure\Repositories\EloquentCartItemRepository;
use Illuminate\Support\ServiceProvider;
use App\Domain\Cart\Repository\CartRepositoryInterface;
use App\Infrastructure\Repositories\EloquentCartRepository;
use App\Domain\Product\Repository\ProductRepositoryInterface;
use App\Infrastructure\Repositories\EloquentProductRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CartRepositoryInterface::class, EloquentCartRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(CartItemRepositoryInterface::class, EloquentCartItemRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(base_path('routes/api.php'));
    }
}
