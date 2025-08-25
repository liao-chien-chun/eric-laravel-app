<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShortUrlRequest;
use App\Http\Resources\ShortUrlResource;
use App\Services\ShortUrlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

/**
 * 短網址 API 控制器 (受 JWT 保護)
 */
class ShortUrlController extends Controller
{
    public function __construct(
        private ShortUrlService $shortUrlService
    ) {}

    /**
     * 建立短網址
     * @param StoreShortUrlRequest $request 驗證後請求
     * @return JsonResponse
     */
    public function store(StoreShortUrlRequest $request): JsonResponse
    {
        try {
            $userId = (int) auth()->id();
            $short = $this->shortUrlService->create($userId, $request->validated());

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_CREATED,
                'message' => '短網址建立成功',
                'data' => new ShortUrlResource($short),
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
                'data' => null
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
