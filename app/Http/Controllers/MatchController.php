<?php

namespace App\Http\Controllers;

use App\Models\MatchModel;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class MatchController extends Controller
{
    public function index()
    {
        $matches = MatchModel::with(['createdBy', 'participants'])
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return Inertia::render('Matches/Index', ['matches' => $matches]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @OA\Post(
     *     path="/api/create-match",
     *     summary="Create Match",
     *     tags={"Matches"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Match information",
     *         @OA\JsonContent(
     *             required={"name", "location", "start_time", "end_time", "skill_level"},
     *             @OA\Property(property="name", type="string", example="Match Name"),
     *             @OA\Property(property="location", type="string", example="Match Location"),
     *             @OA\Property(property="start_time", type="string", format="date-time", example="2024-02-01T12:00:00"),
     *             @OA\Property(property="end_time", type="string", format="date-time", example="2024-02-01T14:00:00"),
     *             @OA\Property(property="skill_level", type="string", enum={"beginner", "intermediate", "advanced"}),
     *             @OA\Property(property="auto_pairing", type="boolean", example=true),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Match created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Match created successfully."),
     *             @OA\Property(property="match", ref="#/components/schemas/Match"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthorized."),
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
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'skill_level' => 'required|in:beginner,intermediate,advanced',
            'auto_pairing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation error. Please check your input.'], 422);
        }

        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $matchData = [
            'user_id' => Auth::id(),
            'name' => $request->name,
            'location' => $request->location,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'skill_level' => $request->skill_level,
            'auto_pairing' => $request->auto_pairing,
        ];

        $match = MatchModel::create($matchData);

        if ($request->auto_pairing && $request->skill_level) {
            $availableParticipants = User::where('availability', 1)
                ->where('skill_level', $request->skill_level)
                ->whereDoesntHave('matches', function ($query) use ($request) {
                    $query->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                })
                ->inRandomOrder()
                ->take(4)
                ->get();

            $match->participants()->attach($availableParticipants);
        }
        return response()->json(['message' => 'Match created successfully.', 'match' => $match], 201);
    }
}
