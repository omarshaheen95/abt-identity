<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function report(Throwable $exception)
    {
        if (app()->environment() == 'production' && !$exception instanceof NotFoundHttpException && !$exception instanceof ValidationException && !$exception instanceof UnauthorizedHttpException && !$exception instanceof AuthenticationException && !$exception instanceof TokenMismatchException)
        {
            $message = $exception->getMessage() .' Code: '.$exception->getCode() . ' File: '. $exception->getFile() . ' Line:'. $exception->getLine();
            Log::channel('telegram')->critical($message);
        }

        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            if (strpos($request->url(), '/api/') !== false){
                return $this->setStatusCode(401)->respondWithError('Unauthenticated');
            }

            if (strpos($request->url(), '/manager/') == true)
            {
                $login = '/manager/login';
            }
            elseif (strpos($request->url(), '/school/') == true)
            {
                $login = '/school/login';
            }
            elseif (strpos($request->url(), '/inspection/') == true)
            {
                $login = '/inspection/login';
            }elseif (strpos($request->url(), '/student/') == true)
            {
                $login = '/student/login';
            }else{
                $login = '/login';
            }
            return redirect()->guest($login);
        }
        if (request()->isXmlHttpRequest() || request()->ajax() || request()->isJson() || request()->wantsJson()) {
            \Log::debug('API Request Exception - '.$request->url().' - '.$exception->getMessage().(!empty($request->all()) ? ' - '.json_encode($request->except(['password'])) : ''));

            if ($exception instanceof AuthorizationException) {
                return $this->setStatusCode(403)->respondWithError($exception->getMessage());
            }

            if ($exception instanceof MethodNotAllowedHttpException) {
                return $this->setStatusCode(403)->respondWithError('Please check HTTP Request Method. - MethodNotAllowedHttpException');
            }

            if ($exception instanceof AuthenticationException) {
                return $this->setStatusCode(401)->respondWithError('Unauthenticated');
            }

            if ($exception instanceof NotFoundHttpException) {
                return $this->setStatusCode(403)->respondWithError('Please check your URL to make sure request is formatted properly. - NotFoundHttpException');
            }



            if ($exception instanceof GeneralException) {
                return $this->setStatusCode(403)->respondWithError($exception->getMessage());
            }

            if ($exception instanceof ModelNotFoundException) {
                return $this->setStatusCode(404)->respondWithError(class_basename($exception->getModel()) . ' Not Found');
            }

            if ($exception instanceof ValidationException) {
                \Log::debug('API Validation Exception - '.json_encode($exception->validator->messages()));
                \Log::alert($exception->validator->messages()->toArray());
                $errors = [];
                $error_message = null;
                $messages = $exception->validator->messages()->toArray();
                if ($exception->validator->fails() || count($messages)) {
                    foreach($messages as $key => $message){
                        $errors[] = ['name' => $key, 'errors' => $message[0]];
                        $error_message = $message[0];
                        break;
                    }
                }
                return $this->setStatusCode(422)->respondWithError($error_message,$errors);
            }

            /*
            * Redirect if token mismatch error
            * Usually because user stayed on the same screen too long and their session expired
            */
            if ($exception instanceof UnauthorizedHttpException) {
                switch (get_class($exception->getPrevious())) {
                    case \App\Exceptions\Handler::class:
                        return $this->setStatusCode($exception->getStatusCode())->respondWithError('Token has not been provided.');
                }
            }else{
                return $this->setStatusCode(500)->respondWithError($exception->getMessage());
            }

        }

        /*
         * All instances of GeneralException redirect back with a flash message to show a bootstrap alert-error
         */
        if ($exception instanceof GeneralException) {
            session()->flash('dontHide', $exception->dontHide);

            return redirect()->back()->withInput()->withFlashDanger($exception->getMessage());
        }

        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            if (strpos($request->url(), '/api/') !== false){
                return response()->json(['User have not permission for this page access.']);
            }else{
                return redirect()->route('manager.home')->with('message', t('User have not permission for this page access.'))->with('m-class', 'error');
            }

        }
        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if (strpos($request->url(), '/api/') !== false){
            return $this->setStatusCode(401)->respondWithError('Unauthenticated');
        }

        $guard = array_get($exception->guards(), 0);
        if (strpos($request->url(), '/api/') !== false){
            return $this->setStatusCode(401)->respondWithError('Unauthenticated');
        }

        if ($guard == 'manager')
        {
            $login = '/manager/login';
        }
        elseif ($guard == 'school')
        {
            $login = '/school/login';
        }
        elseif ($guard == 'inspection')
        {
            $login = '/inspection/login';
        } elseif ($guard == 'student')
        {
            $login = '/student/login';
        }else{
            $login = '/login';
        }
        return redirect()->guest($login);
    }

    /**
     * get the status code.
     *
     * @return statuscode
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * set the status code.
     *
     * @param [type] $statusCode [description]
     *
     * @return statuscode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * respond with error.
     *
     * @param $message
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($message, $errors = [])
    {
        return $this->respond([
            'success' => false,
            'status' => $this->getStatusCode(),
            'message' => $message,
            'errors' => $errors,
        ]);
    }

    /**
     * Respond.
     *
     * @param array $data
     * @param array $headers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $headers = [])
    {
        return response()->json($data, $this->getStatusCode(), $headers);
    }
}
