<?php

namespace App\Exceptions;

use Exception;
use Singsys\LQ\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($request->is('api/*')) {
            return $this->lqRender($request, $exception);
        }

        return parent::render($request, $exception);
    }
}
