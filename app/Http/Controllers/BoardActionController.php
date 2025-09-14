<?php
namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\BoardResource;

class BoardActionController extends Controller
{
    public function reorder(Request $request, Workspace $workspace, Board $board): JsonResponse
    {
        if (Gate::denies('update', $board)){
            return response()->json([
                'message' => 'You are not authorized to reorder this board.'
            ], 403);
        }
        
        $request->validate([
            'position' => 'required|integer|min:0',
        ]);

        $newPosition = $request->input('position');

        $board->update(['position' => $newPosition]);

        $otherBoards = Board::where('workspace_id', $board->workspace_id)
            ->where('id', '!=', $board->id)
            ->orderBy('position')
            ->get();

        $otherBoards->each(function ($item, $index) use ($newPosition) {
            $item->update([
                'position' => $index + ($index >= $newPosition ? 1 : 0),
            ]);
        });

        return response()->json([
            'message'      => 'Board reordered successfully',
            'new_position' => $board->position,
        ]);
    }

    public function toggleFavorite( Workspace $workspace, Board $board): BoardResource
    {
        Gate::authorize('toggleFavorite', $board);
        $board->update([
            'is_favorite' => ! $board->is_favorite,
        ]);

        return new BoardResource($board);
    }
}
