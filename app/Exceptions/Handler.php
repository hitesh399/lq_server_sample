<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use \Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Encryption\DecryptException;
use Symfony\Component\Debug\Exception\FlattenException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];
    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $response = app('Lq\Response');
        $response->message = $exception->getMessage();
        $response->error_code = 'unauthenticated';
        return $response->out(401);
    }
    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

     /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $response = app('Lq\Response');

        if( $exception instanceof ValidationException ) {

            $response->errors = $exception->validator->errors();
            $response->error = $exception->validator->errors()->first();
            return $response->out(422);
        }
        else if($exception instanceof DecryptException) {

            $response->message = $exception->getMessage();
            $response->error_code = 'unable_to_decrypt';
            return $response->out(500);
        }
        else if($exception instanceof OAuthServerException) {

            $response->message = $exception->getMessage();
            $response->error_code = !$response->error_code ? $exception->getErrorType() : $response->error_code;
            return $response->out($exception->getHttpStatusCode());
        }
        else  if($exception instanceof AuthorizationException) {

            $response->message =  'User does have the permission to access this route.';
            $response->error_code = 'forbidden';
            return $response->out(403);
        }
        else if ($request->wantsJson()) {

            // Define the response
            $response->error_code = 'internal_server_error';
            // Add the exception class name, message and stack trace to response
            $response->exception = get_class($exception); // Reflection might be better here
            $response->message = $exception->getMessage();
            $response->trace = $exception->getTrace();

            // Default response of 400
            $status = 400;

            // If this exception is an instance of HttpException
            if ($this->isHttpException($exception)) {
                // Grab the HTTP status code from the Exception
                $status = $exception->getStatusCode();
            }

            // Return a JSON response with the response array and status code
            return $response->out($status);
        }

       return parent::render($request, $exception);
    }
}
