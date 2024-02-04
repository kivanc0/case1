<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class ParticipantController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User Login",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="test@test.com"),
     *             @OA\Property(property="password", type="string", example="password"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="your_access_token_here"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid login information",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid information"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Could not create token",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Could not create token"),
     *         ),
     *     ),
     * )
     */
    public function login(Request $request)
    {
        $info = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($info)) {
                return response()->json(['error' => 'Invalid information'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
        return response()->json(['token' => $token],201);
    }


    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="User Registration",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "skill_level", "availability"},
     *             @OA\Property(property="name", type="string", example="Kıvanç kıvanç"),
     *             @OA\Property(property="email", type="string", format="email", example="test@test.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="skill_level", type="string", enum={"beginner", "intermediate", "advanced"}),
     *             @OA\Property(property="availability", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Participant registered successfully."),
     *             @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation error. Please check your input."),
     *         ),
     *     ),
     * )
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => 'required|min:6|max:255',
            'skill_level' => 'required|in:beginner,intermediate,advanced',
            'availability' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'skill_level' => $request->skill_level,
            'availability' => $request->availability,
        ]);

        $token = JWTAuth::fromUser($user);
        $user->update(['api_token' => $token]);
        return response()->json(['token' => $token], 201);
    }
}
