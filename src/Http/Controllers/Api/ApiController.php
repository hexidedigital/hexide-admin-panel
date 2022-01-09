<?php

namespace HexideDigital\HexideAdmin\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as Controller;

abstract class ApiController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected Request $request;

    protected ?User $user;

    /** Message for responding. Can be the key of translations */
    protected string $message = '';

    /** HTTP header status code */
    protected int $statusCode = 200;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = $request->user();
            return $next($request);
        });
    }

    /** Getter for statusCode */
    protected function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /** Setter for statusCode */
    protected function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /** Set message for response */
    public function setMessage(string $message, bool $translate = true): self
    {
        $this->message = $translate ? __($message) : $message;

        return $this;
    }

    /**
     * Response body: <br>
     * [
     *      'message'     => message,
     *      'http_status' => statusCode,
     * ]
     */
    protected function respondWithMessageStatus(string $message): JsonResponse
    {
        return $this->setMessage($message)->respondWithArray([
            'message' => $this->message,
            'http_status' => $this->statusCode,
        ]);
    }

    /** Respond with success status 200 and massage 'Success' */
    protected function respondWithSuccess(string $message = 'success'): JsonResponse
    {
        return $this->setStatusCode(200)->respondWithMessageStatus($message);
    }

    /** Respond with a given array of items */
    protected function respondWithArray(array $array, array $headers = []): JsonResponse
    {
        return response()->json(['data' => $array], $this->statusCode, $headers);
    }

    /** Response with the current error */
    protected function respondWithError(string $message): JsonResponse
    {
        return $this->setMessage($message)->respondWithArray([
            'message' => $this->message,
            'error' => [
                'http_code' => $this->statusCode,
            ],
        ]);
    }

    /** Generate a Response with a 400 HTTP header and a given message. */
    protected function errorWrongArgs(string $message = 'wrong arguments'): JsonResponse
    {
        return $this->setStatusCode(400)->respondWithError($message);
    }

    /** Generate a Response with a 401 HTTP header and a given message */
    protected function errorUnauthorized(string $message = 'unauthorized'): JsonResponse
    {
        return $this->setStatusCode(401)->respondWithError($message);
    }

    /** Generate a Response with a 403 HTTP header and a given message */
    protected function errorForbidden(string $message = 'forbidden'): JsonResponse
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    /** Generate a Response with a 403 HTTP header and a given message */
    protected function errorLocked(string $message = 'your account is locked'): JsonResponse
    {
        return $this->setStatusCode(403)->respondWithError($message);
    }

    /** Generate a Response with a 404 HTTP header and a given message */
    protected function errorNotFound(string $message = 'resource not found'): JsonResponse
    {
        return $this->setStatusCode(404)->respondWithError($message);
    }

    /** Generate a Response with a 409 HTTP header and a given message */
    protected function hasAlreadyExist(string $message = 'resource has already exist'): JsonResponse
    {
        return $this->setStatusCode(409)->respondWithError($message);
    }

    /** Generate a Response with a 418 HTTP header and a given message */
    protected function errorAuth(string $message = 'auth failed'): JsonResponse
    {
        return $this->setStatusCode(418)->respondWithError($message);
    }

    /** Response with 422 code */
    protected function respondWithValidationErrors(array $errors, string $message = 'given data was invalid', array $headers = []): JsonResponse
    {
        return $this->setStatusCode(422)->setMessage($message)->respondWithArray([
            'message' => $message,
            'errors' => $errors
        ], $headers);
    }

    /** Generate a Response with a 500 HTTP header and a given message */
    protected function errorInternalError(string $message = 'Internal error'): JsonResponse
    {
        return $this->setStatusCode(500)->respondWithError($message);
    }

    /** Get the token array structure */
    protected function respondWithToken(string $token): JsonResponse
    {
        return $this->respondWithArray([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('sanctum.expiration', 0) * 60
        ]);
    }

    /** Get the token array structure with message */
    protected function respondWithTokenWithMessage($token, string $message = 'success'): JsonResponse
    {
        return $this->setMessage($message)->respondWithArray([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('sanctum.expiration', 0) * 60,
            'message' => $this->message,
        ]);
    }
}
