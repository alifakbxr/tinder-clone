<?php

namespace App\Http\Controllers;

use App\Models\Swipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class SwipeController extends Controller
{
     /**
      * @OA\Post(
      *     path="/api/swipes",
      *     operationId="storeSwipe",
      *     tags={"Swipes"},
      *     summary="Store a swipe action",
      *     description="Creates a new swipe record for the authenticated user with either 'like' or 'nope' action.",
      *     security={{"sanctum":{}}},
      *     @OA\RequestBody(
      *         required=true,
      *         @OA\JsonContent(
      *             required={"swiped_id", "action"},
      *             @OA\Property(property="swiped_id", type="integer", description="ID of the user being swiped on"),
      *             @OA\Property(property="action", type="string", enum={"like", "nope"}, description="Swipe action")
      *         )
      *     ),
      *     @OA\Response(
      *         response=201,
      *         description="Swipe stored successfully",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="Swipe stored successfully"),
      *             @OA\Property(
      *                 property="swipe",
      *                 type="object",
      *                 @OA\Property(property="id", type="integer"),
      *                 @OA\Property(property="swiper_id", type="integer"),
      *                 @OA\Property(property="swiped_id", type="integer"),
      *                 @OA\Property(property="action", type="string"),
      *                 @OA\Property(property="created_at", type="string", format="date-time"),
      *                 @OA\Property(property="updated_at", type="string", format="date-time")
      *             )
      *         )
      *     ),
      *     @OA\Response(
      *         response=401,
      *         description="Unauthenticated",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string", example="Unauthenticated.")
      *         )
      *     ),
      *     @OA\Response(
      *         response=422,
      *         description="Validation error",
      *         @OA\JsonContent(
      *             @OA\Property(property="message", type="string"),
      *             @OA\Property(property="errors", type="object")
      *         )
      *     )
      * )
      */
     public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'swiped_id' => 'required|exists:users,id',
            'action' => 'required|in:like,nope',
        ]);

        // Create a new Swipe record
        $swipe = Swipe::create([
            'swiper_id' => Auth::id(),
            'swiped_id' => $validatedData['swiped_id'],
            'action' => $validatedData['action'],
        ]);

        // Return success response with 201 status code
        return response()->json([
            'message' => 'Swipe stored successfully',
            'swipe' => $swipe
        ], HttpResponse::HTTP_CREATED);
    }
}
