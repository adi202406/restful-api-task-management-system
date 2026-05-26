<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\Response;

class WorkspacePolicy
{
    /**
     * Get the user's role for the workspace
     */
    private function getUserRole(User $user, Workspace $workspace): ?string
    {
        // If user is the owner, return 'owner'
        if ($user->id === $workspace->owner_id) {
            return 'owner';
        }

        // Otherwise, check the pivot table
        return $workspace->users()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot
            ?->role;
    }

    /**
     * Determine whether the user can view any workspaces.
     */
    public function viewAny(User $user): bool
    {
        // User can view workspaces they're a member of or own
        return true; // Since index shows only owned workspaces, but we can adjust
    }

    /**
     * Determine whether the user can view the workspace.
     */
    public function view(User $user, Workspace $workspace): bool
    {
        $role = $this->getUserRole($user, $workspace);

        // Only members of the workspace can view it
        return in_array($role, ['owner', 'editor', 'viewer']);
    }

    /**
     * Determine whether the user can create workspaces.
     */
    public function create(User $user): bool
    {
        // Any authenticated user can create workspaces
        return true;
    }

    /**
     * Determine whether the user can update the workspace.
     */
    public function update(User $user, Workspace $workspace): bool
    {
        $role = $this->getUserRole($user, $workspace);

        // Only owners can update workspaces
        return $role === 'owner';
    }

    /**
     * Determine whether the user can delete the workspace.
     */
    public function delete(User $user, Workspace $workspace): bool
    {
        $role = $this->getUserRole($user, $workspace);

        // Only owners can delete workspaces
        return $role === 'owner';
    }

    /**
     * Determine whether the user can invite users to the workspace.
     */
    public function inviteUser(User $user, Workspace $workspace): bool
    {
        $role = $this->getUserRole($user, $workspace);

        // Only owners can invite users
        return $role === 'owner';
    }

    /**
     * Determine whether the user can remove users from the workspace.
     */
    public function removeUser(User $user, Workspace $workspace): bool
    {
        $role = $this->getUserRole($user, $workspace);

        // Only owners can remove users
        return $role === 'owner';
    }

    /**
     * Determine whether the user can accept invitations.
     */
    public function acceptInvitation(User $user, Workspace $workspace): bool
    {
        // Check if user has a pending invitation
        $membership = $workspace->users()
            ->where('user_id', $user->id)
            ->wherePivot('status', 'pending')
            ->first();

        return $membership !== null;
    }
}