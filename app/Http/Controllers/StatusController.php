<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Board;
use App\Models\Workspace;
use App\Http\Requests\StatusRequest;
use App\Http\Resources\StatusResource;

class StatusController extends Controller
{
    public function index(Workspace $workspace, Board $board)
    {
        return StatusResource::collection(
            $board->statuses()->orderBy('position')->get()
        );
    }

    public function store(StatusRequest $request, Workspace $workspace, Board $board)
    {
        $data = $request->validated();

        if ($data['board_id'] !== $board->id) {
            return response()->json([
                'message' => 'board_id must match the current board route'
            ], 422);
        }

        $data['board_id'] = $board->id;

        if (!isset($data['position'])) {
            $data['position'] = ($board->statuses()->max('position') ?? 0) + 1;
        }

        $status = Status::create($data);

        return new StatusResource($status);
    }

    public function show(Workspace $workspace, Board $board, Status $status)
    {
        return new StatusResource($status);
    }

    public function update(StatusRequest $request, Workspace $workspace, Board $board, Status $status)
    {
        $data = $request->validated();

        if ($data['board_id'] !== $board->id) {
            return response()->json([
                'message' => 'board_id must match the current board route'
            ], 422);
        }

        $data['board_id'] = $board->id;
        $status->update($data);

        return new StatusResource($status);
    }

    public function destroy(Workspace $workspace, Board $board, Status $status)
    {
        $status->delete();

        return response()->json([
            'message' => 'Status deleted successfully'
        ], 204);
    }
}
