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
 * )
 */

class Post {}