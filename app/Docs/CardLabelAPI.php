<?php

namespace App\Docs;

/**
 * @OA\Tag(
 *     name="Card Labels",
 *     description="Endpoints to attach/detach labels to cards and list card labels"
 * )
 */
class CardLabelAPI
{
    /**
     * @OA\Post(
     *     path="/cards/attach-label",
     *     summary="Attach a label to a card",
     *     tags={"Card Labels"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CardLabelAttachRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Label attached",
     *         @OA\JsonContent(ref="#/components/schemas/CardLabelResponse")
     *     ),
     *     @OA\Response(response=422, description="Validation error", @OA\JsonContent(ref="#/components/schemas/ErrorResponse"))
     * )
     */
    public function attach() {}

    /**
     * @OA\Post(
     *     path="/cards/detach-label",
     *     summary="Detach a label from a card",
     *     tags={"Card Labels"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"card_id","label_id"},
     *             @OA\Property(property="card_id", type="integer", example=1),
     *             @OA\Property(property="label_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Label detached", @OA\JsonContent(ref="#/components/schemas/SuccessResponse"))
     * )
     */
    public function detach() {}

    /**
     * @OA\Get(
     *     path="/cards/{cardId}/labels",
     *     summary="Get labels for a card",
     *     tags={"Card Labels"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="cardId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="List of labels",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/LabelResource")
     *         )
     *     )
     * )
     */
    public function getCardLabels() {}
}
