<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as Controller;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

abstract class ApiController extends Controller
{
    /** Message for responding. Can be the key of translations */
    protected string $message = '';

    /** Set message for response */
    public function setMessage(string $message, bool $translate = true): self
    {
        $this->message = $translate ? __($message) : $message;

        return $this;
    }

    protected function respondMessage(string $message, int $status = SymfonyResponse::HTTP_OK): JsonResponse
    {
        return $this->setMessage($message)->respondArray([
            'message' => $this->message,
        ], $status);
    }

    /** Respond with success status 200 and massage 'Success' */
    protected function respondSuccess(string $message = 'success', int $status = SymfonyResponse::HTTP_OK): JsonResponse
    {
        return $this->respondMessage($message, $status);
    }

    /** Respond without content with status 204 */
    protected function respondNoContent(): JsonResponse
    {
        return response()->json('', SymfonyResponse::HTTP_NO_CONTENT);
    }

    /** Respond with a given array of items */
    protected function respondDataArray(array $array, int $status = SymfonyResponse::HTTP_OK, array $headers = []): JsonResponse
    {
        return $this->respondArray(['data' => $array], $status, $headers);
    }

    /** Respond with a given array of items */
    protected function respondArray(array $array, int $status = SymfonyResponse::HTTP_OK, array $headers = []): JsonResponse
    {
        return response()->json($array, $status, $headers);
    }

    /** Response with the current error */
    protected function respondWithError(string $message, int $statusCode): JsonResponse
    {
        return $this->setMessage($message)->respondArray([
            'message' => $this->message,
        ], $statusCode);
    }

    /** Generate a Response with a 400 HTTP header and a given message. */
    protected function errorWrongArgs(string $message = 'wrong arguments', int $statusCode = SymfonyResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 401 HTTP header and a given message */
    protected function errorUnauthorized(string $message = 'unauthorized', int $statusCode = SymfonyResponse::HTTP_UNAUTHORIZED): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 403 HTTP header and a given message */
    protected function errorForbidden(string $message = 'forbidden', int $statusCode = SymfonyResponse::HTTP_FORBIDDEN): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 403 HTTP header and a given message */
    protected function errorLocked(string $message = 'your account is locked', int $statusCode = SymfonyResponse::HTTP_FORBIDDEN): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 404 HTTP header and a given message */
    protected function errorNotFound(string $message = 'resource not found', int $statusCode = SymfonyResponse::HTTP_NOT_FOUND): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 409 HTTP header and a given message */
    protected function errorAlreadyExist(string $message = 'resource has already exist', int $statusCode = SymfonyResponse::HTTP_CONFLICT): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 418 HTTP header and a given message */
    protected function errorAuth(string $message = 'auth failed', int $statusCode = SymfonyResponse::HTTP_I_AM_A_TEAPOT): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Response with 422 code */
    protected function respondValidationErrors(array $errors, string $message = 'given data was invalid', int $statusCode = SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY, array $headers = []): JsonResponse
    {
        return $this->setMessage($message)->respondDataArray([
            'message' => $this->message,
            'errors' => $errors,
        ], $statusCode, $headers);
    }

    /** Generate a Response with a 500 HTTP header and a given message */
    protected function errorInternalError(string $message = 'Internal server error', int $statusCode = SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Get the token array structure */
    protected function respondToken(string $token): JsonResponse
    {
        return $this->respondDataArray([
            'access_token' => $token,
        ]);
    }

    /** Get the token array structure with message */
    protected function respondTokenAndMessage($token, string $message = 'success'): JsonResponse
    {
        return $this->setMessage($message)->respondDataArray([
            'access_token' => $token,
            'message' => $this->message,
        ]);
    }
}
