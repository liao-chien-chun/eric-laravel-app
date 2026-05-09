<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateItemRequest;
use App\Http\Requests\GetItemsRequest;
use App\Http\Resources\AdminItemResource;
use App\Services\ItemService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(
        private ItemService $itemService
    ) {}

    /**
     * 取得指定狀態的商品列表（分頁）
     * 只有管理者可以呼叫
     *
     * @param GetItemsRequest $request 驗證後的請求資料
     * @return JsonResponse
     */
    public function index(GetItemsRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $status = $validated['status'] ?? 2; // 預設取得上架的商品
            $perPage = $validated['per_page'] ?? 15; // 預設每頁 15 筆

            $items = $this->itemService->getItemsByStatus($status, $perPage);

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_OK,
                'message' => '商品列表取得成功',
                'data' => [
                    'items' => AdminItemResource::collection($items->items()),
                    'pagination' => [
                        'current_page' => $items->currentPage(),
                        'per_page' => $items->perPage(),
                        'total' => $items->total(),
                        'last_page' => $items->lastPage(),
                    ],
                ],
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => '伺服器錯誤，請稍後再試',
                'data' => null
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * 建立商品
     * @param CreateItemRequest $request
     * @return JsonResponse 
     */
    public function store(CreateItemRequest $request): JsonResponse
    {
        try {
            $item = $this->itemService->createItem($request->validated());

            return response()->json([
                'success' => true,
                'status' => Response::HTTP_CREATED,
                'message' => '商品建立成功',
                'data' => new AdminItemResource($item)
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status' => $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => '伺服器錯誤，請稍後再試',
                'data' => null
            ], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
