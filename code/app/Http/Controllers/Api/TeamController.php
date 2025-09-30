<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\AddTeamMemberRequest;
use App\Http\Requests\SelectSubjectRequest;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    protected TeamService $teamService;

    public function __construct(TeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Display a listing of teams.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $filters = $request->only(['status', 'grade', 'search']);
        $perPage = $request->get('per_page', 15);

        $teams = $this->teamService->getTeamsForUser($user, $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $teams,
        ]);
    }

    /**
     * Store a newly created team.
     */
    public function store(CreateTeamRequest $request): JsonResponse
    {
        try {
            $team = $this->teamService->createTeam(
                $request->validated(),
                $request->user()
            );

            return response()->json([
                'success' => true,
                'message' => 'Team created successfully',
                'data' => $team->load('members.student'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create team',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified team.
     */
    public function show(Team $team): JsonResponse
    {
        $team->load([
            'members.student',
            'subject.teacher',
            'project.supervisor',
            'externalProject'
        ]);

        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }

    /**
     * Update the specified team.
     */
    public function update(Request $request, Team $team): JsonResponse
    {
        try {
            // //$this->authorize('update', $team);

            $team->update($request->only(['name', 'description']));

            return response()->json([
                'success' => true,
                'message' => 'Team updated successfully',
                'data' => $team->fresh()->load('members.student'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update team',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified team.
     */
    public function destroy(Team $team): JsonResponse
    {
        try {
            //$this->authorize('delete', $team);

            $this->teamService->deleteTeam($team);

            return response()->json([
                'success' => true,
                'message' => 'Team deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete team',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add a member to the team.
     */
    public function addMember(AddTeamMemberRequest $request, Team $team): JsonResponse
    {
        try {
            $member = \App\Models\User::findOrFail($request->input('student_id'));

            $this->teamService->addMemberToTeam(
                $team,
                $member,
                $request->input('role', 'member')
            );

            return response()->json([
                'success' => true,
                'message' => 'Member added successfully',
                'data' => $team->fresh()->load('members.student'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove a member from the team.
     */
    public function removeMember(Team $team, $memberId): JsonResponse
    {
        try {
            $member = \App\Models\User::findOrFail($memberId);

            $this->teamService->removeMemberFromTeam($team, $member);

            return response()->json([
                'success' => true,
                'message' => 'Member removed successfully',
                'data' => $team->fresh()->load('members.student'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove member',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Select a subject for the team.
     */
    public function selectSubject(SelectSubjectRequest $request, Team $team): JsonResponse
    {
        try {
            $subject = \App\Models\Subject::findOrFail($request->input('subject_id'));

            $result = $this->teamService->selectSubject(
                $team,
                $subject,
                $request->input('motivation')
            );

            return response()->json([
                'success' => true,
                'message' => 'Subject selection processed',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to select subject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get team invitations for current user.
     */
    public function invitations(Request $request): JsonResponse
    {
        $user = $request->user();
        $invitations = $this->teamService->getPendingInvitations($user);

        return response()->json([
            'success' => true,
            'data' => $invitations,
        ]);
    }

    /**
     * Accept team invitation.
     */
    public function acceptInvitation(Team $team): JsonResponse
    {
        try {
            $user = request()->user();
            $this->teamService->acceptInvitation($team, $user);

            return response()->json([
                'success' => true,
                'message' => 'Invitation accepted successfully',
                'data' => $team->fresh()->load('members.student'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept invitation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Decline team invitation.
     */
    public function declineInvitation(Team $team): JsonResponse
    {
        try {
            $user = request()->user();
            $this->teamService->declineInvitation($team, $user);

            return response()->json([
                'success' => true,
                'message' => 'Invitation declined',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to decline invitation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
