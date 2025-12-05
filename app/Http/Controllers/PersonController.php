<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Interaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/people/{personId}/recommended",
     *     summary="Get recommended people",
     *     description="Get list of recommended people with pagination. Excludes people that the current person has already interacted with.",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="personId",
     *         in="path",
     *         description="ID of the current person",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found"
     *     )
     * )
     */
    public function getRecommended(Request $request, $personId)
    {
        try {
            // Check if person exists
            $currentPerson = Person::find($personId);
            if (!$currentPerson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Person not found'
                ], 404);
            }

            // Get IDs of people already interacted with
            $interactedIds = Interaction::where('from_person_id', $personId)
                ->pluck('to_person_id')
                ->toArray();

            // Get recommended people (excluding self and already interacted)
            $perPage = $request->input('per_page', 10);
            
            $recommended = Person::where('id', '!=', $personId)
                ->whereNotIn('id', $interactedIds)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $recommended
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching recommended people',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/interactions/like",
     *     summary="Like a person",
     *     description="Record that one person likes another person",
     *     tags={"Interactions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"from_person_id","to_person_id"},
     *             @OA\Property(property="from_person_id", type="integer", example=1),
     *             @OA\Property(property="to_person_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person liked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Person liked successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function likePerson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_person_id' => 'required|exists:people,id',
            'to_person_id' => 'required|exists:people,id|different:from_person_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Check if interaction already exists
            $existingInteraction = Interaction::where('from_person_id', $request->from_person_id)
                ->where('to_person_id', $request->to_person_id)
                ->first();

            if ($existingInteraction) {
                // Update existing interaction
                if ($existingInteraction->type !== 'like') {
                    $existingInteraction->update(['type' => 'like']);
                    
                    // Increment likes count
                    Person::where('id', $request->to_person_id)->increment('likes_count');
                }
            } else {
                // Create new like interaction
                Interaction::create([
                    'from_person_id' => $request->from_person_id,
                    'to_person_id' => $request->to_person_id,
                    'type' => 'like',
                ]);

                // Increment likes count
                Person::where('id', $request->to_person_id)->increment('likes_count');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Person liked successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error liking person',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/interactions/dislike",
     *     summary="Dislike a person",
     *     description="Record that one person dislikes another person",
     *     tags={"Interactions"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"from_person_id","to_person_id"},
     *             @OA\Property(property="from_person_id", type="integer", example=1),
     *             @OA\Property(property="to_person_id", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Person disliked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Person disliked successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function dislikePerson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_person_id' => 'required|exists:people,id',
            'to_person_id' => 'required|exists:people,id|different:from_person_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Check if interaction already exists
            $existingInteraction = Interaction::where('from_person_id', $request->from_person_id)
                ->where('to_person_id', $request->to_person_id)
                ->first();

            if ($existingInteraction) {
                // Update existing interaction
                if ($existingInteraction->type === 'like') {
                    // Decrement likes count if changing from like to dislike
                    Person::where('id', $request->to_person_id)->decrement('likes_count');
                }
                $existingInteraction->update(['type' => 'dislike']);
            } else {
                // Create new dislike interaction
                Interaction::create([
                    'from_person_id' => $request->from_person_id,
                    'to_person_id' => $request->to_person_id,
                    'type' => 'dislike',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Person disliked successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error disliking person',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/people/{personId}/liked-by",
     *     summary="Get liked by list",
     *     description="Get list of people who liked the current person",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="personId",
     *         in="path",
     *         description="ID of the person",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found"
     *     )
     * )
     */
    public function getLikedByList(Request $request, $personId)
    {
        try {
            // Check if person exists
            $currentPerson = Person::find($personId);
            if (!$currentPerson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Person not found'
                ], 404);
            }

            // Get people who liked this person
            $perPage = $request->input('per_page', 10);
            
            $likedBy = Person::whereHas('interactionsGiven', function ($query) use ($personId) {
                $query->where('to_person_id', $personId)
                      ->where('type', 'like');
            })
            ->with(['interactionsGiven' => function ($query) use ($personId) {
                $query->where('to_person_id', $personId)
                      ->where('type', 'like');
            }])
            ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $likedBy
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching liked by list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/people/{personId}/disliked-by",
     *     summary="Get disliked by list",
     *     description="Get list of people who disliked the current person",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="personId",
     *         in="path",
     *         description="ID of the person",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found"
     *     )
     * )
     */
    public function getDislikedByList(Request $request, $personId)
    {
        try {
            // Check if person exists
            $currentPerson = Person::find($personId);
            if (!$currentPerson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Person not found'
                ], 404);
            }

            // Get people who disliked this person
            $perPage = $request->input('per_page', 10);
            
            $dislikedBy = Person::whereHas('interactionsGiven', function ($query) use ($personId) {
                $query->where('to_person_id', $personId)
                      ->where('type', 'dislike');
            })
            ->with(['interactionsGiven' => function ($query) use ($personId) {
                $query->where('to_person_id', $personId)
                      ->where('type', 'dislike');
            }])
            ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $dislikedBy
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching disliked by list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/people/{personId}/disliked",
     *     summary="Get disliked people list",
     *     description="Get list of people that the current person has disliked",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="personId",
     *         in="path",
     *         description="ID of the person",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found"
     *     )
     * )
     */
    public function getDislikedList(Request $request, $personId)
    {
        try {
            // Check if person exists
            $currentPerson = Person::find($personId);
            if (!$currentPerson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Person not found'
                ], 404);
            }

            // Get people that this person has disliked
            $perPage = $request->input('per_page', 10);
            
            $disliked = Person::whereHas('interactionsReceived', function ($query) use ($personId) {
                $query->where('from_person_id', $personId)
                      ->where('type', 'dislike');
            })
            ->with(['interactionsReceived' => function ($query) use ($personId) {
                $query->where('from_person_id', $personId)
                      ->where('type', 'dislike');
            }])
            ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $disliked
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching disliked list',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/people",
     *     summary="Get all people",
     *     description="Get paginated list of all people (for testing)",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="John Doe"),
     *                         @OA\Property(property="age", type="integer", example=25),
     *                         @OA\Property(property="pictures", type="array", @OA\Items(type="string")),
     *                         @OA\Property(property="location", type="string", example="Jakarta"),
     *                         @OA\Property(property="likes_count", type="integer", example=0),
     *                         @OA\Property(property="email_sent", type="boolean", example=false)
     *                     )
     *                 ),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=15)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 15);
            $people = Person::paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $people
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching people',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/people/{id}",
     *     summary="Get a single person",
     *     description="Get details of a specific person",
     *     tags={"People"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the person",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="age", type="integer", example=25),
     *                 @OA\Property(property="pictures", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="location", type="string", example="Jakarta"),
     *                 @OA\Property(property="likes_count", type="integer", example=0),
     *                 @OA\Property(property="email_sent", type="boolean", example=false)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Person not found"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $person = Person::find($id);

            if (!$person) {
                return response()->json([
                    'success' => false,
                    'message' => 'Person not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $person
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching person',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
