<?php
namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Label;
use App\Http\Resources\LabelResource;
use App\Http\Requests\CardLabelRequest;

class CardLabelController extends Controller
{
    public function attach(CardLabelRequest $request)
    {
        $card = Card::with('board.workspace')->findOrFail($request->card_id);
        $label = Label::findOrFail($request->label_id);

        if ($card->board->workspace_id !== $label->workspace_id) {
            return response()->json([
                'message' => 'Label and card must belong to the same workspace',
            ], 422);
        }

        $card->labels()->syncWithoutDetaching([$request->label_id]);

        return response()->json([
            'message' => 'Label attached to card successfully',
        ]);
    }

    public function detach(CardLabelRequest $request)
    {
        $card = Card::with('board.workspace')->findOrFail($request->card_id);
        $label = Label::findOrFail($request->label_id);

        if ($card->board->workspace_id !== $label->workspace_id) {
            return response()->json([
                'message' => 'Label and card must belong to the same workspace',
            ], 422);
        }

        $card->labels()->detach($request->label_id);

        return response()->json([
            'message' => 'Label detached from card successfully',
        ]);
    }

    public function getCardLabels($cardId)
    {
        $card = Card::with('labels')->findOrFail($cardId);
        return LabelResource::collection($card->labels);
    }
}
