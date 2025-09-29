<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\CreateTeamRequest;
use App\Http\Requests\PFE\UpdateTeamRequest;
use App\Http\Requests\PFE\TeamPreferenceRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeamController extends Controller
{
    public function __construct(private TeamService $teamService)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Display a listing of teams
     */
    public function index(Request $request): JsonResponse
    {
        $query = Team::with(['leader:id,first_name,last_name,department', 'members.user:id,first_name,last_name']);

        // Apply filters
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('department')) {
            $query->whereHas('leader', function ($q) use ($request) {
                $q->where('department', $request->department);
            });
        }

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $teams = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $teams->items(),
            'meta' => [
                'current_page' => $teams->currentPage(),
                'total' => $teams->total(),
                'per_page' => $teams->perPage(),
                'last_page' => $teams->lastPage()
            ]
        ]);
    }

    /**
     * Store a newly created team
     */
    public function store(CreateTeamRequest $request): JsonResponse
    {
        $team = $this->teamService->createTeam(
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'team' => $team->load(['leader:id,first_name,last_name', 'members.user:id,first_name,last_name']),
            'message' => 'Team created successfully'
        ], 201);
    }

    /**
     * Display the specified team
     */
    public function show(Team $team): JsonResponse
    {
        $this->authorize('view', $team);

        $team->load([
            'leader:id,first_name,last_name,department',
            'members.user:id,first_name,last_name,student_id',
            'subjectPreferences.subject:id,title,supervisor_id',
            'project.subject:id,title'
        ]);

        return response()->json([
            'team' => $team,
            'members' => $team->members,
            'preferences' => $team->subjectPreferences,
            'project' => $team->project
        ]);
    }

    /**
     * Update the specified team
     */
    public function update(UpdateTeamRequest $request, Team $team): JsonResponse
    {
        $action = $request->get('action', 'update_info');

        switch ($action) {
            case 'add_member':
                $updatedTeam = $this->teamService->addMember($team, $request->user_id);
                $message = 'Member added successfully';
                break;

            case 'remove_member':
                $updatedTeam = $this->teamService->removeMember($team, $request->user_id);
                $message = 'Member removed successfully';
                break;

            default:
                $team->update($request->only(['name']));
                $updatedTeam = $team;
                $message = 'Team updated successfully';
        }

        return response()->json([
            'team' => $updatedTeam->load(['leader:id,first_name,last_name', 'members.user:id,first_name,last_name']),
            'message' => $message
        ]);
    }

    /**
     * Add a member to the team
     */
    public function addMember(Request $request, Team $team): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $this->authorize('manage', $team);

        $updatedTeam = $this->teamService->addMember($team, $request->user_id);

        $member = User::find($request->user_id);

        return response()->json([
            'member' => [
                'id' => $member->id,
                'first_name' => $member->first_name,
                'last_name' => $member->last_name,
                'student_id' => $member->student_id
            ],
            'team' => $updatedTeam,
            'message' => 'Member added successfully'
        ], 201);
    }

    /**
     * Remove a member from the team
     */
    public function removeMember(Team $team, User $user): JsonResponse
    {
        $this->authorize('manage', $team);

        $updatedTeam = $this->teamService->removeMember($team, $user->id);

        return response()->json([
            'team' => $updatedTeam,
            'message' => 'Member removed successfully'
        ]);
    }

    /**
     * Set team subject preferences
     */
    public function setPreferences(TeamPreferenceRequest $request, Team $team): JsonResponse
    {
        // Clear existing preferences
        $team->subjectPreferences()->delete();

        // Add new preferences
        $preferences = [];
        foreach ($request->preferences as $preference) {
            $preferences[] = $team->subjectPreferences()->create([
                'subject_id' => $preference['subject_id'],
                'preference_order' => $preference['preference_order']
            ]);
        }

        return response()->json([
            'preferences' => $preferences,
            'message' => 'Preferences saved successfully'
        ]);
    }

    /**
     * Validate a team
     */
    public function validateTeam(Team $team): JsonResponse
    {
        $this->authorize('validate', $team);

        $validatedTeam = $this->teamService->validateTeam($team);

        return response()->json([
            'team' => $validatedTeam,
            'message' => 'Team validated successfully'
        ]);
    }

    /**
     * Get user's team
     */
    public function myTeam(Request $request): JsonResponse
    {
        $user = $request->user();
        $teamMembership = $user->teamMemberships()->with([
            'team.leader:id,first_name,last_name',
            'team.members.user:id,first_name,last_name,student_id',
            'team.project.subject:id,title'
        ])->first();

        if (!$teamMembership) {
            return response()->json([
                'team' => null,
                'message' => 'You are not a member of any team'
            ]);
        }

        return response()->json([
            'team' => $teamMembership->team,
            'role' => $teamMembership->role
        ]);
    }

    /**
     * Get available teams for assignment
     */
    public function available(): JsonResponse
    {
        $teams = $this->teamService->getAvailableTeams();

        return response()->json([
            'teams' => $teams
        ]);
    }
}