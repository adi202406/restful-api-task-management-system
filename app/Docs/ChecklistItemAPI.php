<?php

namespace App\Docs;

/**
 * @OA\Tag(
 *     name="Checklist Items",
 *     description="Manage checklist items"
 * )
 */
class ChecklistItemAPI
{
    /**
     * @OA\Get(
     *     path="/checklists/{checklist}/items",
     *     summary="List items for a checklist",
     *     tags={"Checklist Items"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Items list", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ChecklistItemResource")))
     * )
     */
    public function index() {}

    /**
     * @OA\Post(
     *     path="/checklists/{checklist}/items",
     *     summary="Create a checklist item",
     *     tags={"Checklist Items"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ChecklistItemRequest")),
     *     @OA\Response(response=201, description="Item created", @OA\JsonContent(ref="#/components/schemas/ChecklistItemResource"))
     * )
     */
    public function store() {}

    /**
     * @OA\Get(
     *     path="/checklists/{checklist}/items/{item}",
     *     summary="Get a checklist item",
     *     tags={"Checklist Items"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="item", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Item detail", @OA\JsonContent(ref="#/components/schemas/ChecklistItemResource"))
     * )
     */
    public function show() {}

    /**
     * @OA\Put(
     *     path="/checklists/{checklist}/items/{item}",
     *     summary="Update a checklist item",
     *     tags={"Checklist Items"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="item", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/ChecklistItemRequest")),
     *     @OA\Response(response=200, description="Item updated", @OA\JsonContent(ref="#/components/schemas/ChecklistItemResource"))
     * )
     */
    public function update() {}

    /**
     * @OA\Delete(
     *     path="/checklists/{checklist}/items/{item}",
     *     summary="Delete a checklist item",
     *     tags={"Checklist Items"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="item", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Item deleted", @OA\JsonContent(ref="#/components/schemas/SuccessResponse"))
     * )
     */
    public function destroy() {}

    /**
     * @OA\Put(
     *     path="/checklists/{checklist}/items",
     *     summary="Bulk update checklist items",
     *     tags={"Checklist Items"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="checklist", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/BulkChecklistItemUpdateRequest")),
     *     @OA\Response(response=200, description="Bulk update result", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/ChecklistItemResource")))
     * )
     */
    public function bulkUpdate() {}
}
