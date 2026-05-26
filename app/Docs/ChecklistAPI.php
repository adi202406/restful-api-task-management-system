<?php

namespace App\Docs;

/**
 * @OA\Tag(
 *     name="Checklists",
 *     description="Manage checklists"
 * )
 */
class ChecklistAPI
{
    /**
     * @OA\Get(
     *     path="/checklists",
     *     summary="List checklists",
     *     tags={"Checklists"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="List of checklists", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ChecklistResource")))
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/checklists",
     *     summary="Create a checklist",
     *     tags={"Checklists"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ChecklistRequest")),
     *     @OA\Response(response=201, description="Checklist created", @OA\JsonContent(ref="#/components/schemas/ChecklistResource")),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/checklists/{checklist}",
     *     summary="Get checklist",
     *     tags={"Checklists"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Checklist detail", @OA\JsonContent(ref="#/components/schemas/ChecklistResource"))
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/checklists/{checklist}",
     *     summary="Update checklist",
     *     tags={"Checklists"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ChecklistRequest")),
     *     @OA\Response(response=200, description="Checklist updated", @OA\JsonContent(ref="#/components/schemas/ChecklistResource"))
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/checklists/{checklist}",
     *     summary="Delete checklist",
     *     tags={"Checklists"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Checklist deleted", @OA\JsonContent(ref="#/components/schemas/SuccessResponse"))
     * )
     */
    public function destroy() {}

    /**
     * @OA\Put(
     *     path="/checklists/{checklist}/position/{position}",
     *     summary="Update checklist position",
     *     tags={"Checklists"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="position", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Position updated", @OA\JsonContent(ref="#/components/schemas/ChecklistResource"))
     * )
     */
    public function updatePosition() {}
}
