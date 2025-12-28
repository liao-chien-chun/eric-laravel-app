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
     * 取得我的短網址清單
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = (int) auth()->id();
            $perPage = (int) $request->query('per_page', 15);

            $shortUrls = $this->shortUrlService->getMyShortUrls($userId, $perPage);

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '短網址清單取得成功',
                'data' => [
                    'items' => ShortUrlResource::collection($shortUrls->items()),
                    'pagination' => [
                        'current_page' => $shortUrls->currentPage(), // 目前第幾頁
                        'per_page' => $shortUrls->perPage(), // 一頁有幾筆資料
                        'total' => $shortUrls->total(), // 全部資料總筆數
                        'last_page' => $shortUrls->lastPage(), // 最後一頁是第幾頁
                    ],
                ],
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $e->getMessage(),
                'data' => null
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

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
