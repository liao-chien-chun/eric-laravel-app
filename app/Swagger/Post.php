<?php

namespace App\Swagger;

/**
 * @OA\Post(
 *      path="/api/posts",
 *      summary="新增文章",
 *      tags={"Post"},
 *      description="已登入之使用者才能新增文章",
 *      operationId="createPost",
 *      security={{"bearerAuth":{}}},
 *      
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(ref="#/components/schemas/StorePostRequest")
 *      ),
 * 
 *      @OA\Response(
 *          response=201,
 *          description="文章建立成功",
 *          @OA\JsonContent(ref="#/components/schemas/PostResource")
 *      ),
 * 
 *      @OA\Response(
 *         response=401,
 *         description="尚未授權，請登入",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="message", type="string", example="尚未授權，請登入"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *      ),
 * 
 *      @OA\Response(
 *          response=422,
 *          description="驗證失敗",
 *          @OA\JsonContent(
 *          @OA\Property(property="success", type="boolean", example=false),
 *              @OA\Property(property="status", type="integer", example=422),
 *              @OA\Property(property="message", type="string", example="驗證失敗"),
 *              @OA\Property(
 *                  property="errors",
 *                  type="object",
 *                  @OA\Property(
 *                      property="title",
 *                      type="array",
 *                      @OA\Items(type="string", example="標題為必填")
 *                  ),
 *                  @OA\Property(
 *                      property="content",
 *                      type="array",
 *                      @OA\Items(type="string", example="文章內容為必填")
 *                  ),
 *                  @OA\Property(
 *                      property="status",
 *                      type="array",
 *                      @OA\Items(type="string", example="狀態為必填")
 *                  )
 *              )
 *          )
 *      ),
 * 
 *      @OA\Response(
 *          response=500,
 *          description="伺服器錯誤",
 *          @OA\JsonContent(
 *              @OA\Property(property="success", type="boolean", example=false),
 *              @OA\Property(property="status", type="integer", example=500),
 *              @OA\Property(property="message", type="string", example="伺服器錯誤，請稍後再試"),
 *              @OA\Property(property="data", type="string", nullable=true, example=null)
 *          )
 *      )
 * ),
 * 
 * @OA\Put(
 *     path="/api/posts/{post}",
 *     summary="更新文章",
 *     tags={"Post"},
 *     description="已登入使用者可更新自己建立的文章。若文章狀態已發布，則無法改為草稿。",
 *     operationId="updatePost",
 *     security={{"bearerAuth":{}}},
 *  
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="文章 ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *  
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/StorePostRequest")
 *     ),
 * 
 *     @OA\Response(
 *         response=200,
 *         description="文章更新成功",
 *         @OA\JsonContent(ref="#/components/schemas/PostResource")
 *     ),
 * 
 *     @OA\Response(
 *         response=403,
 *         description="無權限修改文章",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="你沒有權限修改該文章"),
 *             @OA\Property(property="data", type="string", example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="找不到該文章",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="找不到該文章"),
 *             @OA\Property(property="data", type="string", example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="驗證失敗或無法變更狀態",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="status", type="integer", example=422),
 *                     @OA\Property(property="message", type="string", example="已發布的文章不能更改為草稿狀態"),
 *                     @OA\Property(property="data", type="string", example=null)
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="status", type="integer", example=422),
 *                     @OA\Property(property="message", type="string", example="驗證失敗"),
 *                     @OA\Property(
 *                         property="errors",
 *                         type="object",
 *                         @OA\Property(
 *                             property="title",
 *                             type="array",
 *                             @OA\Items(type="string", example="標題會必填")
 *                         ),
 *                         @OA\Property(
 *                             property="content",
 *                             type="array",
 *                             @OA\Items(type="string", example="文章內容為必填")
 *                         ),
 *                         @OA\Property(
 *                             property="status",
 *                             type="array",
 *                             @OA\Items(type="string", example="狀態為必填")
 *                         )
 *                     )
 *                 )
 *             }
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="伺服器錯誤",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=500),
 *             @OA\Property(property="message", type="string", example="伺服器錯誤請稍後再試"),
 *             @OA\Property(property="data", type="string", example=null)
 *         )
 *     )
 * ),
 *
 * @OA\Delete(
 *     path="/api/posts/{post}",
 *     summary="刪除文章",
 *     tags={"Post"},
 *     description="已登入使用者可刪除自己建立的文章",
 *     operationId="deletePost",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="文章 ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="文章刪除成功",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="message", type="string", example="文章刪除成功"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="尚未授權，請登入",
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
 *         description="無權限刪除文章",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="你沒有權限刪除該文章"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="找不到該文章",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="找不到該文章"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
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
 * @OA\Patch(
 *     path="/api/posts/{post}/status",
 *     summary="更新文章狀態",
 *     tags={"Post"},
 *     description="已登入使用者可更新自己建立的文章狀態。狀態轉換規則：草稿(1)→發布(2)、發布(2)→隱藏(3)、隱藏(3)→發布(2)。不可將已發布或隱藏的文章改回草稿。",
 *     operationId="updatePostStatus",
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Parameter(
 *         name="post",
 *         in="path",
 *         description="文章 ID",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(
 *                 property="status",
 *                 type="integer",
 *                 description="文章狀態 (1:草稿, 2:發布, 3:隱藏)",
 *                 enum={1, 2, 3},
 *                 example=2
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="文章狀態更新成功",
 *         @OA\JsonContent(ref="#/components/schemas/PostResource")
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="尚未授權，請登入",
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
 *         description="無權限修改文章",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=403),
 *             @OA\Property(property="message", type="string", example="你沒有權限修改該文章"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="找不到該文章",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="status", type="integer", example=404),
 *             @OA\Property(property="message", type="string", example="找不到該文章"),
 *             @OA\Property(property="data", type="string", nullable=true, example=null)
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="驗證失敗或狀態轉換不合法",
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="status", type="integer", example=422),
 *                     @OA\Property(property="message", type="string", example="驗證失敗"),
 *                     @OA\Property(
 *                         property="errors",
 *                         type="object",
 *                         @OA\Property(
 *                             property="status",
 *                             type="array",
 *                             @OA\Items(type="string", example="狀態僅允許 1(草稿)、2(發布)、3(隱藏)")
 *                         )
 *                     )
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="status", type="integer", example=422),
 *                     @OA\Property(property="message", type="string", example="不允許將文章從「發布」狀態變更為「草稿」狀態"),
 *                     @OA\Property(property="data", type="string", nullable=true, example=null)
 *                 ),
 *                 @OA\Schema(
 *                     @OA\Property(property="success", type="boolean", example=false),
 *                     @OA\Property(property="status", type="integer", example=422),
 *                     @OA\Property(property="message", type="string", example="新狀態與目前狀態相同"),
 *                     @OA\Property(property="data", type="string", nullable=true, example=null)
 *                 )
 *             }
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
 * )
 */

class Post {}