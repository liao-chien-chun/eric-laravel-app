<?php

namespace App\Swagger;

/**
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
