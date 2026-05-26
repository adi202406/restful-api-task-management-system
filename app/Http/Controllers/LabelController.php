<?php

namespace App\Http\Controllers;

use App\Models\Label;
use App\Models\Workspace;
use Illuminate\Http\Request;
use App\Http\Requests\LabelRequest;
use App\Http\Resources\LabelResource;

class LabelController extends Controller
{
    public function index(Workspace $workspace)
    {
        $labels = $workspace->labels()->get();
        return LabelResource::collection($labels);
    }

    public function store(LabelRequest $request, Workspace $workspace)
    {
        $data = $request->validated();

        if ($data['workspace_id'] !== $workspace->id) {
            return response()->json([
                'message' => 'workspace_id must match the current workspace route'
            ], 422);
        }

        $data['workspace_id'] = $workspace->id;
        $label = Label::create($data);

        return new LabelResource($label);
    }

    public function show(Workspace $workspace, Label $label)
    {
        return new LabelResource($label);
    }

    public function update(LabelRequest $request, Workspace $workspace, Label $label)
    {
        $data = $request->validated();

        if ($data['workspace_id'] !== $workspace->id) {
            return response()->json([
                'message' => 'workspace_id must match the current workspace route'
            ], 422);
        }

        $data['workspace_id'] = $workspace->id;
        $label->update($data);

        return new LabelResource($label);
    }

    public function destroy(Workspace $workspace, Label $label)
    {
        $label->delete();
        return response()->json([
            'message' => 'Label deleted successfully'
        ], 204);
    }
}
