<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/short-urls",
 *     summary="取得我的短網址清單",
 *     tags={"ShortUrl"},
 *     description="已登入之使用者取得自己建立的短網址清單（分頁）",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="每頁筆數",
 *         required=false,
 *         @OA\Schema(type="integer", example=15)
 *     ),
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="頁碼",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="短網址清單取得成功",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="status",  type="integer", example=200),
 *             @OA\Property(property="message", type="string",  example="短網址清單取得成功"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(
 *                     property="items",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id",           type="integer", example=1),
 *                         @OA\Property(property="short_code",   type="string",  example="abc123"),
 *                         @OA\Property(property="original_url", type="string",  example="https://www.youtube.com/watch?v=nuRP6Xu-QG4"),
 *                         @OA\Property(property="click_count",  type="integer", example=10),
 *                         @OA\Property(property="expired_at",   type="string",  format="date-time", nullable=true, example="2025-12-12 12:00:00"),
 *                         @OA\Property(property="created_at",   type="string",  format="date-time", example="2025-08-12 00:55:29"),
 *                         @OA\Property(property="updated_at",   type="string",  format="date-time", example="2025-08-12 00:55:29")
 *                     )
 *                 ),
 *                 @OA\Property(
 *                     property="pagination",
 *                     type="object",
 *                     @OA\Property(property="current_page", type="integer", example=1),
 *                     @OA\Property(property="per_page",     type="integer", example=15),
 *                     @OA\Property(property="total",        type="integer", example=7),
 *                     @OA\Property(property="last_page",    type="integer", example=1)
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="尚未授權，請登入（無效的JWT）",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status",  type="integer", example=401),
 *             @OA\Property(property="message", type="string",  example="尚未授權，請登入"),
 *             @OA\Property(property="data",    type="string",  nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="系統錯誤",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status",  type="integer", example=500),
 *             @OA\Property(property="message", type="string",  example="伺服器錯誤，請稍後再試"),
 *             @OA\Property(property="data",    type="string",  nullable=true, example=null)
 *         )
 *     )
 * ),
 *
 * @OA\Post(
 *     path="/api/short-urls",
 *     summary="建立短網址",
 *     tags={"ShortUrl"},
 *     description="已登入之使用者才能建立短網址",
 *     security={{"bearerAuth":{}}},
 *
 * @OA\RequestBody(
 *     required=true,
 *     description="用於新增短網址的欄位",
 *     @OA\MediaType(
 *         mediaType="application/json",
 *         @OA\Schema(
 *             title="新增短網址請求欄位",
 *             type="object",
 *             required={"original_url"},
 *             @OA\Property(property="original_url", type="string", maxLength=2048, example="https://example.com/page?a=1"),
 *             @OA\Property(property="short_code",   type="string", nullable=true, description="自訂短碼（英數 4~32）", example="erictest01"),
 *             @OA\Property(property="expired_at",   type="string", format="date-time", nullable=true, example="2025-12-12 12:00:00")
 *           )
 *       )
 *   ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="短網址建立成功",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="status",  type="integer", example=201),
 *             @OA\Property(property="message", type="string",  example="短網址建立成功"),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id",           type="integer", example=1),
 *                 @OA\Property(property="short_code",   type="string",  example="erictest01"),
 *                 @OA\Property(property="original_url", type="string",  example="https://www.youtube.com/watch?v=nuRP6Xu-QG4"),
 *                 @OA\Property(property="click_count",  type="integer", example=0),
 *                 @OA\Property(property="expired_at",   type="string",  format="date-time", nullable=true, example="2025-12-12 12:00:00"),
 *                 @OA\Property(property="created_at",   type="string",  format="date-time", example="2025-08-12 00:55:29"),
 *                 @OA\Property(property="updated_at",   type="string",  format="date-time", example="2025-08-12 00:55:29")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="尚未授權，請登入（無效的JWT）",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status",  type="integer", example=401),
 *             @OA\Property(property="message", type="string",  example="尚未授權，請登入"),
 *             @OA\Property(property="data",    type="string",  nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="驗證或業務規則失敗",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     title="欄位驗證失敗",
 *                     type="object",
 *                     example={
 *                         "success": false,
 *                         "status": 422,
 *                         "message": "驗證失敗",
 *                         "errors": {
 *                             "original_url": {"原始網址為必填","原始網址必須為正確之 URL 格式"},
 *                             "short_code":   {"短碼僅能包含英數字，長度 4~32。","短網址重複"},
 *                             "expired_at":   {"過期時間必須比今日晚","過期時間必須為日期格式 xxxx-yy-zz 00:00:00"}
 *                         },
 *                         "data": null
 *                     }
 *                 ),
 *                 @OA\Schema(
 *                     title="業務規則：短碼為系統保留字",
 *                     type="object",
 *                     example={
 *                         "success": false,
 *                         "status": 422,
 *                         "message": "該短碼為系統保留字",
 *                         "data": null
 *                     }
 *                 ),
 *                 @OA\Schema(
 *                     title="業務規則：短碼已被使用",
 *                      type="object",
 *                     example={
 *                         "success": false,
 *                         "status": 422,
 *                         "message": "該短碼已被使用",
 *                         "data": null
 *                     }
 *                 )
 *             }
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="系統錯誤（DB 寫入失敗、未預期例外）",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status",  type="integer", example=500),
 *             @OA\Property(property="message", type="string",  example="伺服器錯誤，請稍後再試"),
 *             @OA\Property(property="errors",  type="string",  nullable=true, example=null)
 *         )
 *     )
 * )
 */

class ShortUrl {}
