<?php

declare(strict_types=1);

namespace HexideDigital\HexideAdmin\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as Controller;

abstract class ApiController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use Includeble;

    protected ?User $user;

    /** Message for responding. Can be the key of translations */
    protected string $message = '';

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = $request->user();
            return $next($request);
        });
    }

    /** Set message for response */
    public function setMessage(string $message, bool $translate = true): self
    {
        $this->message = $translate ? __($message) : $message;

        return $this;
    }

    protected function respondMessage(string $message, int $status = 200): JsonResponse
    {
        return $this->setMessage($message)->respondArray([
            'message' => $this->message,
        ], $status);
    }

    /** Respond with success status 200 and massage 'Success' */
    protected function respondSuccess(string $message = 'success', int $status = 200): JsonResponse
    {
        return $this->respondMessage($message, $status);
    }

    /** Respond without content with status 204 */
    protected function respondNoContent()
    {
        return response()->noContent();
    }

    /** Respond with a given array of items */
    protected function respondDataArray(array $array, int $status = 200, array $headers = []): JsonResponse
    {
        return $this->respondArray(['data' => $array], $status, $headers);
    }

    /** Respond with a given array of items */
    protected function respondArray(array $array, int $status = 200, array $headers = []): JsonResponse
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
    protected function errorWrongArgs(string $message = 'wrong arguments', int $statusCode = 400): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 401 HTTP header and a given message */
    protected function errorUnauthorized(string $message = 'unauthorized', int $statusCode = 401): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 403 HTTP header and a given message */
    protected function errorForbidden(string $message = 'forbidden', int $statusCode = 403): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 403 HTTP header and a given message */
    protected function errorLocked(string $message = 'your account is locked', int $statusCode = 403): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 404 HTTP header and a given message */
    protected function errorNotFound(string $message = 'resource not found', int $statusCode = 404): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 409 HTTP header and a given message */
    protected function errorAlreadyExist(string $message = 'resource has already exist', int $statusCode = 409): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Generate a Response with a 418 HTTP header and a given message */
    protected function errorAuth(string $message = 'auth failed', int $statusCode = 418): JsonResponse
    {
        return $this->respondWithError($message, $statusCode);
    }

    /** Response with 422 code */
    protected function respondValidationErrors(array $errors, string $message = 'given data was invalid', int $statusCode = 422, array $headers = []): JsonResponse
    {
        return $this->setMessage($message)->respondDataArray([
            'message' => $this->message,
            'errors' => $errors,
        ], $statusCode, $headers);
    }

    /** Generate a Response with a 500 HTTP header and a given message */
    protected function errorInternalError(string $message = 'Internal error', int $statusCode = 500): JsonResponse
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
