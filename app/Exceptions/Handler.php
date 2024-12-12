<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use App\Domain\Product\Exceptions\ProductNotFoundException;
use App\Domain\Cart\Exceptions\CartNotFoundException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<class-string<Throwable>>
     */
    protected $dontReport = [];

    /**
     * Report or log an exception.
     *
     * @param  Throwable  $e
     * @return void
     *
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof ProductNotFoundException) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 404);
        }

        if ($e instanceof CartNotFoundException) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 404);
        }

        return parent::render($request, $e);
    }
}
