<?php

namespace App\Exceptions;

use Exception;
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
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
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
        if($exception instanceof \Illuminate\Session\TokenMismatchException){
            return redirect()
                ->back()
                ->withInput($request->except('_token'))
                ->withError('Il semble que le formulaire ait expiré, merci de réessayer. M');
        }
        return parent::render($request, $exception);
    }

    protected function convertExceptionToResponse(Exception $e)
    {
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        
        switch ($statusCode) {
            case 500:
                $title = 'Désolé, la page que vous recherchez n\'a pas pu être trouvée.';
                break;
            default:
                $title = 'Oups ! Une erreur s\'est produite, contactez l\'administrateur ...';
        }
 
 
        $debug = config('app.debug', false);
 
        if ($debug) {
            return parent::convertExceptionToResponse($e);
        }
 
        return response()->view('vendor.error.500', ['exception' => $e, 'title' => $title], $statusCode);
    }
}
