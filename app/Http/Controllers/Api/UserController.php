<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\RegisterUserResource;
use App\Http\Resources\UserLoginResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    /**
     * 註冊新使用者
     * 
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request):JsonResponse
    {
        try {
            $user = $this->userService->register($request->validated());

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_CREATED,
                'message' => '註冊成功',
                'data' =>  new RegisterUserResource($user),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => '註冊失敗: ' . $e->getMessage(),
                'data' => null, 
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 使用者登入
     * 
     * @param UserLoginRequest $request
     * @return JsonResponse
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->login($request->validated());

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '登入成功',
                'data' => new UserLoginResource($result),
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode() ? : 500,
                'message' => $e->getMessage(),
                'data' => null,
            ], $e->getCode() ? : 500);
        }
    }
}
