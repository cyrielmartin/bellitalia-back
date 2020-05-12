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
    // Cette méthode permet de créer une erreur en cas de manipulation volontaire d'un fichier envoyé :
    // ex: transformer un pdf artificiellement en jpg
    if ($exception instanceof \Illuminate\Http\Exceptions\PostTooLargeException) {
      // En faisant la procédure classique, impossible de récupérer l'erreur en front
      // return response('File too large!', 422);
      // Alors j'ai fait ça :
      return back()->withErrors('Server error');
    }
    return parent::render($request, $exception);
  }
}
