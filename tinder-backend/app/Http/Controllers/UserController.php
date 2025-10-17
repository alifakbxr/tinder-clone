<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
     /**
      * @OA\Get(
      *     path="/api/users/liked",
      *     operationId="getLikedUsers",
      *     tags={"Users"},
      *     summary="Get liked users",
      *     description="Returns a paginated list of users that the authenticated user has liked.",
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
     public function liked(Request $request)
    {
        $user = $request->user();

        // Get the IDs of users that the authenticated user has liked
        $likedUserIds = DB::table('swipes')
            ->where('swiper_id', $user->id)
            ->where('action', 'like')
            ->pluck('swiped_id');

        // Get paginated list of users with their pictures
        $likedUsers = User::whereIn('id', $likedUserIds)
            ->with('pictures')
            ->paginate(20);

        return UserResource::collection($likedUsers);
   }

   /**
    * Login user and return token
    */
   public function login(Request $request)
   {
       $request->validate([
           'email' => 'required|email',
           'password' => 'required',
       ]);

       $user = User::where('email', $request->email)->first();

       if (!$user || !Hash::check($request->password, $user->password)) {
           throw ValidationException::withMessages([
               'email' => ['The provided credentials are incorrect.'],
           ]);
       }

       // Create token for the user
       $token = $user->createToken('api-token')->plainTextToken;

       return response()->json([
           'token' => $token,
           'user' => new UserResource($user->load('pictures')),
       ]);
   }
}
