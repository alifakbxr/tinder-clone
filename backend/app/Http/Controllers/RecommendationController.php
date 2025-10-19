<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Tinder Clone API",
 *     version="1.0.0",
 *     description="API documentation for the Tinder Clone application",
 *     @OA\Contact(
 *         email="admin@tinder-clone.com"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Laravel API Server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="apiKey",
 *     name="Authorization",
 *     in="header",
 *     description="Laravel Sanctum Token"
 * )
 */

class RecommendationController extends Controller
{
     /**
      * @OA\Get(
      *     path="/api/users/recommendations",
      *     operationId="getUserRecommendations",
      *     tags={"Recommendations"},
      *     summary="Get user recommendations",
      *     description="Returns a paginated list of user recommendations for the authenticated user, excluding users they've already swiped on.",
      *     security={{"sanctum":{}}},
      *     @OA\Parameter(
      *         name="page",
      *         in="query",
      *         description="Page number for pagination",
      *         required=false,
      *         @OA\Schema(type="integer", minimum=1, default=1)
      *     ),
      *     @OA\Response(
      *         response=200,
      *         description="Successful operation",
      *         @OA\JsonContent(
      *             @OA\Property(
      *                 property="data",
      *                 type="array",
      *                 @OA\Items(
      *                     @OA\Property(property="id", type="integer"),
      *                     @OA\Property(property="name", type="string"),
      *                     @OA\Property(property="age", type="integer"),
      *                     @OA\Property(property="latitude", type="number", format="float"),
      *                     @OA\Property(property="longitude", type="number", format="float"),
      *                     @OA\Property(property="pictures", type="array", @OA\Items(type="object")),
      *                     @OA\Property(property="created_at", type="string", format="date-time"),
      *                     @OA\Property(property="updated_at", type="string", format="date-time")
      *                 )
      *             ),
      *             @OA\Property(property="links", type="object"),
      *             @OA\Property(property="meta", type="object")
      *         )
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="Unauthenticated",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="Unauthenticated.")
      *         )
      *     )
      * )
      */
     public function index(Request $request)
    {
        // Get the authenticated user
        $authenticatedUser = $request->user();

        // Fetch the IDs of all users the authenticated user has already swiped on
        $swipedUserIds = $authenticatedUser->swipes()->pluck('swiped_id')->toArray();

        // Build the query for users to recommend
        $recommendedUsers = User::with('pictures')
            ->where('id', '!=', $authenticatedUser->id) // Exclude the authenticated user
            ->whereNotIn('id', $swipedUserIds) // Exclude users they've already swiped on
            ->paginate(10); // Return paginated results (10 per page)

        // Return the paginated results as a JSON resource collection
        return UserResource::collection($recommendedUsers);
    }
}
