<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Workspace;
use App\Http\Requests\BoardRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\BoardResource;

class BoardController extends Controller
{
    public function index(Workspace $workspace)
    {
        $this->authorize('viewAny', [Board::class, $workspace]);

        $boards = $workspace->boards()
            ->orderBy('position')
            ->withCount('cards')
            ->get();

        return BoardResource::collection($boards);
    }

    public function store(BoardRequest $request, Workspace $workspace)
    {
        $this->authorize('create', [Board::class, $workspace]);

        $board = Board::create([
            ...$request->validated(),
            'workspace_id' => $workspace->id,
        ]);

        return new BoardResource($board);
    }
    public function show(Workspace $workspace, Board $board)
    {

        return new BoardResource($board->load(['workspace', 'cards']));
    }

    public function update(BoardRequest $request, Workspace $workspace, Board $board)
    {
        if (Gate::allows('update', $board)) {
            $validated = $request->validated();
            $validated['workspace_id'] = $workspace->id;

            $board->update($validated);

            return new BoardResource($board);
        }

        return response()->json([
            'message' => 'You are not authorized to update this board.'
        ], 403);
    }

    public function destroy(Workspace $workspace, Board $board)
    {
        if (Gate::denies('delete', $board)) {
            return response()->json([
                'message' => 'You are not authorized to delete this board.'
            ], 403);
        }

        $board->delete();

        return response()->json(null, 204);
    }
}
