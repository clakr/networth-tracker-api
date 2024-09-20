<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MissingUserQueryParametersException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => 'ERROR: Missing `user` Query Parameters',
        ], Response::HTTP_BAD_REQUEST);
    }
}
