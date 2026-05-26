<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use App\Models\UserDevice;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\WorkspaceRequest;
use App\Http\Resources\WorkspaceResource;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use App\Jobs\SendWorkspaceInvitationNotification;
use App\Notifications\WorkspaceInvitationNotification;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\WorkspaceInvitation;

class WorkspaceController extends Controller
{
    // GET /api/workspaces
    public function index()
    {
        $workspaces = Workspace::where(function ($query) {
            $query->where('owner_id', Auth::id())
                ->orWhereHas('users', function ($q) {
                    $q->where('user_id', Auth::id());
                });
        })
            ->withCount('users')
            ->with('workspaceUsers')
            ->get();

        if ($workspaces->isEmpty()) {
            return response()->json(['message' => 'You do not have any workspaces.'], 404);
        }
        return WorkspaceResource::collection($workspaces);
    }

    // POST /api/workspaces
    public function store(WorkspaceRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Generate unique slug if not provided
            $slug = Str::slug($validated['title']);
            $slug = $this->generateUniqueSlug($slug);

            $workspace = Workspace::create([
                'title'       => $validated['title'],
                'description' => $validated['description'],
                'slug'        => $slug,
                'visibility'  => $validated['visibility'],
                'owner_id'    => Auth::id(),
            ]);

            if ($request->hasFile('banner_image')) {
                // Upload new banner to Cloudinary
                $uploadResult = Cloudinary::upload($request->file('banner_image')->getRealPath());

                // Get the secure URL and public ID
                $uploadedFileUrl = $uploadResult->getSecurePath();
                $publicId        = $uploadResult->getPublicId();

                // Update workspace with banner image info
                $workspace->banner_image           = $uploadedFileUrl;
                $workspace->banner_image_public_id = $publicId;
                $workspace->save();
            }

            // Sync workspace creator as owner in pivot table
            $workspace->users()->attach(Auth::id(), [
                'role'      => 'owner',
                'status'    => 'active',
                'joined_at' => now(),
            ]);

            DB::commit();

            return new WorkspaceResource($workspace);
        } catch (\Throwable $th) {
            DB::rollBack();
            // Log::error($th); // optional: log error for debugging
            return response()->json([
                'message' => 'Gagal menyimpan workspace.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    // GET /api/workspaces/{id}
    public function show($slug)
    {
        $workspace = Workspace::where('slug', $slug)->firstOrFail();

        $this->authorize('view', $workspace);

        return new WorkspaceResource($workspace->load(['workspaceUsers.user', 'owner']));
    }

    // PUT /api/workspaces/{id}
    public function update(WorkspaceRequest $request, $id)
    {
        $workspace = Workspace::findOrFail($id);

        $this->authorize('update', $workspace);

        $validated = $request->validated();

        // Generate unique slug if not provided
        $slug = Str::slug($validated['title']);
        $slug = $this->generateUniqueSlug($slug);

        $workspace->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'slug'        => $slug,
            'visibility'  => $validated['visibility'],
        ]);

        if ($request->hasFile('banner_image')) {
            // If user already has an banner_image, delete the old one
            if ($workspace->banner_image_public_id) {
                // Delete old image from Cloudinary
                Cloudinary::destroy($workspace->banner_image_public_id);
            }
            // Upload new banner_image to Cloudinary
            $uploadResult = Cloudinary::upload($request->file('banner_image')->getRealPath());

            // Get the secure URL and public ID
            $uploadedFileUrl = $uploadResult->getSecurePath();
            $publicId        = $uploadResult->getPublicId();

            // Update wo$workspace's banner_image and banner_image_public_id
            $workspace->banner_image           = $uploadedFileUrl;
            $workspace->banner_image_public_id = $publicId;
            $workspace->save();
        }

        return new WorkspaceResource($workspace);
    }

    // DELETE /api/workspaces/{id}
    public function destroy($id)
    {
        $workspace = Workspace::findOrFail($id);

        $this->authorize('delete', $workspace);

        $workspace->delete();

        return response()->json(['message' => 'Workspace deleted successfully.']);
    }

    public function inviteUser(Request $request, $id)
    {
        $workspace = Workspace::findOrFail($id);

        $this->authorize('inviteUser', $workspace);

        // Validate input
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role'  => 'required|in:editor,viewer',
        ]);

        // Get the invited user
        $user = User::where('email', $validated['email'])->first();

        // Prepare notification data
        $notificationData = [
            'title'        => 'Workspace Invitation',
            'body'         => 'You have been invited to join "' . $workspace->title . '" as ' . $validated['role'],
            'workspace_id' => $workspace->id,
            'type'         => 'workspace_invitation',
        ];

        // Include soft-deleted pivot record in the check
        $existingMember = $workspace->users()
            ->withPivot('status', 'deleted_at')
            ->wherePivot('user_id', $user->id)
            ->withTrashed()
            ->first();

        // Case: user still in workspace
        if ($existingMember && is_null($existingMember->pivot->deleted_at)) {
            if ($existingMember->pivot->status === 'pending') {
                return response()->json([
                    'message' => 'User has already been invited and the invitation is pending',
                ], 422);
            }

            if ($existingMember->pivot->status === 'active') {
                return response()->json([
                    'message' => 'User is already an active member of this workspace',
                ], 422);
            }
        }

        // Prepare notification data
        $notificationData = [
            'title'        => 'Workspace Invitation',
            'body'         => 'You have been invited to join "' . $workspace->name . '" as ' . $validated['role'],
            'workspace_id' => $workspace->id,
            'type'         => 'workspace_invitation',
        ];

        $token = Str::uuid()->toString();
        // Case: user was removed (soft deleted) before — restore and update data
        if ($existingMember && ! is_null($existingMember->pivot->deleted_at)) {
            $workspace->users()->updateExistingPivot($user->id, [
                'role'       => $validated['role'],
                'status'     => 'pending',
                'invitation_token' => $token,
                'invitation_expires_at' => now()->addDays(7),
                'invitation_accepted_at' => null,
                'joined_at'  => null,
                'deleted_at' => null,
            ]);


            $user->notify(
                new WorkspaceInvitationNotification(
                    $workspace,
                    auth()->user(),
                    $validated['role'],
                    $token
                )
            );

            // Send push notification
            SendWorkspaceInvitationNotification::dispatch($user, $notificationData);

            return response()->json([
                'message' => 'User has been re-invited successfully',
            ], 200);
        }


        $workspace->users()->attach($user->id, [
            'role' => $validated['role'],
            'status' => 'pending',
            'invited_by' => auth()->id(),
            'invitation_token' => $token,
            'invitation_expires_at' => now()->addDays(7),
        ]);

        $user->notify(
            new WorkspaceInvitationNotification(
                $workspace,
                auth()->user(),
                $validated['role'],
                $token
            )
        );

        // Send push notification
        SendWorkspaceInvitationNotification::dispatch($user, $notificationData);

        return response()->json([
            'message' => 'Invitation sent successfully',
        ], 201);
    }



    private function sendPushNotification($user, array $data)
    {
        try {
            $messaging = app('firebase.messaging');

            // Dapatkan semua device token user yang diundang
            $deviceTokens = UserDevice::where('user_id', $user->id)
                ->pluck('device_token')
                ->toArray();

            if (empty($deviceTokens)) {
                return false;
            }

            $notification = Notification::create($data['title'], $data['body']);

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData([
                    'type' => $data['type'],
                    'workspace_id' => $data['workspace_id'],
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK' // Untuk aplikasi Flutter
                ]);

            // Kirim ke semua device user
            $messaging->sendMulticast($message, $deviceTokens);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send push notification: ' . $e->getMessage());
            return false;
        }
    }

    public function acceptInvitation(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
        ]);

        $membership = DB::table('workspace_user')
            ->where('invitation_token', $request->token)
            ->first();

        if (!$membership) {
            return response()->json([
                'message' => 'Invitation not found',
            ], 404);
        }

        if ($membership->invitation_accepted_at) {
            return response()->json([
                'message' => 'Invitation already used',
            ], 422);
        }

        if (
            $membership->invitation_expires_at &&
            now()->greaterThan($membership->invitation_expires_at)
        ) {
            return response()->json([
                'message' => 'Invitation expired',
            ], 422);
        }

        if ($membership->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized invitation',
            ], 403);
        }

        DB::table('workspace_user')
            ->where('id', $membership->id)
            ->update([
                'status' => 'active',
                'joined_at' => now(),
                'invitation_accepted_at' => now(),
                'updated_at' => now(),
            ]);

        $workspace = Workspace::findOrFail(
            $membership->workspace_id
        );

        return response()->json([
            'message' => 'Invitation accepted',
            'workspace' => [
                'id' => $workspace->id,
                'slug' => $workspace->slug,
            ],
        ]);
    }
    public function removeUser(Request $request, $id)
    {
        $workspace = Workspace::findOrFail($id);

        $this->authorize('removeUser', $workspace);

        // Validate input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if user exists in workspace
        $membership = $workspace->users()
            ->where('user_id', $validated['user_id'])
            ->wherePivot('status', 'active')
            ->firstOrFail();

        // Prevent removing workspace owner
        if ($membership->pivot->role === 'owner') {
            return response()->json([
                'message' => 'Cannot remove workspace owner',
            ], 403);
        }

        // Soft delete the user from workspace and update status
        $workspace->users()->updateExistingPivot($validated['user_id'], [
            'status'     => 'removed',
            'deleted_at' => now(),
        ]);

        return response()->json([
            'message' => 'User has been removed from workspace successfully',
        ], 200);
    }

    // Generate a unique slug if already exists
    private function generateUniqueSlug($title)
    {
        $baseSlug = Str::slug($title);

        if (empty($baseSlug)) {
            $baseSlug = 'workspace';
        }

        do {
            $suffix = strtolower(substr(Str::ulid(), -6));

            $slug = "{$baseSlug}-{$suffix}";
        } while (
            Workspace::where('slug', $slug)->exists()
        );

        return $slug;
    }
}
