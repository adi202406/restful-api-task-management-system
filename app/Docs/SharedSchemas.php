<?php

namespace App\Docs;

/**
 * Shared OpenAPI component schemas used by Cards and Checklists docs
 */
class SharedSchemas
{
    /**
     * @OA\Schema(
     *     schema="SuccessResponse",
     *     @OA\Property(property="message", type="string", example="Success")
     * )
     */
    public function success() {}

    /**
     * @OA\Schema(
     *     schema="LabelResource",
     *     @OA\Property(property="id", type="integer", example=2),
     *     @OA\Property(property="name", type="string", example="bug"),
     *     @OA\Property(property="color", type="string", example="#ff0000")
     * )
     */
    public function label() {}

    /**
     * @OA\Schema(
     *     schema="CardLabelAttachRequest",
     *     required={"card_id","label_id"},
     *     @OA\Property(property="card_id", type="integer", example=1),
     *     @OA\Property(property="label_id", type="integer", example=2)
     * )
     */
    public function cardLabelAttachReq() {}

    /**
     * @OA\Schema(
     *     schema="CardLabelResponse",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="card_id", type="integer", example=1),
     *     @OA\Property(property="label", ref="#/components/schemas/LabelResource")
     * )
     */
    public function cardLabelResp() {}

    /**
     * @OA\Schema(
     *     schema="CardAssignmentRequest",
     *     required={"user_id"},
     *     @OA\Property(property="user_id", type="integer", example=5)
     * )
     */
    public function cardAssignReq() {}

    /**
     * @OA\Schema(
     *     schema="CardAssignmentResource",
     *     @OA\Property(property="user_id", type="integer", example=5),
     *     @OA\Property(property="name", type="string", example="Jane"),
     *     @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *     @OA\Property(property="assigned_at", type="string", format="date-time", example="2026-05-24T12:00:00Z")
     * )
     */
    public function cardAssignRes() {}

    /**
     * @OA\Schema(
     *     schema="ChecklistRequest",
     *     required={"card_id","title"},
     *     @OA\Property(property="card_id", type="integer", example=1),
     *     @OA\Property(property="title", type="string", example="Pre-flight checks"),
     *     @OA\Property(property="position", type="integer", example=1)
     * )
     */
    public function checklistReq() {}

    /**
     * @OA\Schema(
     *     schema="ChecklistResource",
     *     @OA\Property(property="id", type="integer", example=10),
     *     @OA\Property(property="title", type="string", example="Pre-flight checks"),
     *     @OA\Property(property="position", type="integer", example=1),
     *     @OA\Property(property="items", type="array", @OA\Items(ref="#/components/schemas/ChecklistItemResource"))
     * )
     */
    public function checklistRes() {}

    /**
     * @OA\Schema(
     *     schema="ChecklistItemRequest",
     *     required={"content"},
     *     @OA\Property(property="content", type="string", example="Check fuel"),
     *     @OA\Property(property="position", type="integer", example=1),
     *     @OA\Property(property="is_completed", type="boolean", example=false)
     * )
     */
    public function checklistItemReq() {}

    /**
     * @OA\Schema(
     *     schema="ChecklistItemResource",
     *     @OA\Property(property="id", type="integer", example=100),
     *     @OA\Property(property="content", type="string", example="Check fuel"),
     *     @OA\Property(property="is_completed", type="boolean", example=false),
     *     @OA\Property(property="position", type="integer", example=1)
     * )
     */
    public function checklistItemRes() {}

    /**
     * @OA\Schema(
     *     schema="BulkChecklistItemUpdateRequest",
     *     @OA\Property(
     *         property="items",
     *         type="array",
     *         @OA\Items(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="is_completed", type="boolean")
     *         )
     *     )
     * )
     */
    public function bulkUpdateReq() {}
}
