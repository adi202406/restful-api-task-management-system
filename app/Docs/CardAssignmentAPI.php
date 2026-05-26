<?php

namespace App\Docs;

/**
 * @OA\Tag(
 *     name="Card Assignments",
 *     description="Manage assignees for cards"
 * )
 */
class CardAssignmentAPI
{
    /**
     * @OA\Get(
     *     path="/cards/{card}/assignees",
     *     summary="List assignees for a card",
     *     tags={"Card Assignments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="card", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Assignees list", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CardAssignmentResource")))
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/cards/{card}/assignees",
     *     summary="Assign a user to a card",
     *     tags={"Card Assignments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="card", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/CardAssignmentRequest")),
     *     @OA\Response(response=201, description="Assignee created", @OA\JsonContent(ref="#/components/schemas/CardAssignmentResource")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function store() {}

    /**
     * @OA\Delete(
     *     path="/cards/{card}/assignees/{user}",
     *     summary="Remove an assignee from a card",
     *     tags={"Card Assignments"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="card", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Assignee removed", @OA\JsonContent(ref="#/components/schemas/SuccessResponse"))
     * )
     */
    public function destroy() {}
}
