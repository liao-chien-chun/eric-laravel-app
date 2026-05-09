<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/items",
 *     summary="管理員取得商品列表",
 *     tags={"Item"},
 *     description="管理員取得商品列表 API，支援分頁與狀態篩選，需要管理員權限",
 *     operationId="getItems",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="status",
 *         in="query",
 *         description="商品狀態 (1:草稿, 2:上架, 3:下架)，預設為 2 (上架)",
 *         required=false,
 *         @OA\Schema(
 *             type="integer",
 *             enum={1, 2, 3},
 *             default=2
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="每頁筆數，預設 15，最多 100",
 *         required=false,
 *         @OA\Schema(
 *             type="integer",
 *             minimum=1,
 *             maximum=100,
 *             default=15
 *         )
 *     ),
 *
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="頁碼，預設 1",
 *         required=false,
 *         @OA\Schema(
 *             type="integer",
 *             minimum=1,
 *             default=1
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="商品列表取得成功",
 *         @OA\JsonContent(ref="#/components/schemas/GetItemsResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="尚未授權",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="尚未授權，請登入"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=403,
 *         description="權限不足",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="權限不足，僅限管理者使用"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="驗證失敗",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=422),
 *             @OA\Property(property="message", type="string", example="驗證失敗"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="status",
 *                     type="array",
 *                     @OA\Items(type="string", example="狀態僅允許 1(草稿)、2(上架)、3(下架)")
 *                 ),
 *                 @OA\Property(
 *                     property="per_page",
 *                     type="array",
 *                     @OA\Items(type="string", example="每頁筆數最多為 100")
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="伺服器錯誤",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="伺服器錯誤，請稍後再試"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * ),
 *
 * @OA\Post(
 *     path="/api/items",
 *     summary="建立商品",
 *     tags={"Item"},
 *     description="管理員建立新商品 API，需要管理員權限",
 *     operationId="createItem",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/CreateItemRequest")
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="商品建立成功",
 *         @OA\JsonContent(ref="#/components/schemas/CreateItemResponse")
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="尚未授權",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="尚未授權，請登入"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=403,
 *         description="權限不足",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="權限不足，僅限管理者使用"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="驗證失敗",
 *         @OA\JsonContent(ref="#/components/schemas/CreateItemValidationError")
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="伺服器錯誤",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="商品建立失敗：內部伺服器錯誤"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     )
 * )
 */

class Item {}
