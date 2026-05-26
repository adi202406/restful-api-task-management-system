<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Board;
use App\Models\Status;
use App\Http\Requests\CardRequest;
use App\Http\Resources\CardResource;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CardController extends Controller
{

    public function index(
        Workspace $workspace,
        Board $board
    ) {
        $this->authorize('view', $board);

        Log::info('Fetching cards for board: ' . $board->cards);

        return CardResource::collection(
            $board->cards
        );
    }
    public function store(CardRequest $request, Workspace $workspace, Board $board)
    {
        $validated = $request->validated();
        $validated['board_id'] = $board->id;

        if (isset($validated['status_id']) && $validated['status_id'] !== null) {
            $status = Status::where('id', $validated['status_id'])
                ->where('board_id', $board->id)
                ->first();

            if (!$status) {
                return response()->json([
                    'message' => 'The selected status_id is invalid for this board.'
                ], 422);
            }
        }

        // Set default position if not provided
        if (!isset($validated['position'])) {
            $lastPosition = Card::where('board_id', $board->id)->max('position') ?? 0;
            $validated['position'] = $lastPosition + 1;
        }

        $card = Card::create($validated);

        $card->users()->attach(Auth::id(), [
            'assigned_at' => now()
        ]);

        return new CardResource($card);
    }

    public function show(
        Workspace $workspace,
        Board $board,
        Card $card
    ) {
        return new CardResource($card);
    }

    public function update(
        CardRequest $request,
        Workspace $workspace,
        Board $board,
        Card $card
    ) {
        $validated = $request->validated();

        if (array_key_exists('status_id', $validated) && $validated['status_id'] !== null) {
            $status = Status::where('id', $validated['status_id'])
                ->where('board_id', $board->id)
                ->first();

            if (!$status) {
                return response()->json([
                    'message' => 'The selected status_id is invalid for this board.'
                ], 422);
            }
        }

        $card->update($validated);
        return new CardResource($card);
    }

    public function destroy(
        Workspace $workspace,
        Board $board,
        Card $card
    ) {
        $card->delete();
        return response()->json([
            'message' => 'Card deleted successfully'
        ]);
    }
}
