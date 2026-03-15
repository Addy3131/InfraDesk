<?php

namespace App\Services;

use App\Models\Workspace;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class WorkspaceService
{
    public function create(User $user, string $name): Workspace
    {
        return DB::transaction(function () use ($user, $name) {

            $workspace = Workspace::create([
                'name' => $name,
                'owner_id' => $user->id,
            ]);

            $workspace->users()->attach($user->id, [
                'role' => 'owner'
            ]);

            return $workspace;
        });
    }
}
