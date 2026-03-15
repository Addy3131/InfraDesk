<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WorkspaceService;
use App\Models\Workspace;

class WorkspaceController extends Controller
{
   public function store(Request $request, WorkspaceService $service)
    {
        
        $this->authorize('create', Workspace::class);

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $workspace = $service->create($request->user(), $request->name);

        return response()->json($workspace, 201);
    }
}
