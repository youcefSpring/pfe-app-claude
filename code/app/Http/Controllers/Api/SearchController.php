<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Team;
use App\Models\PfeProject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Global search across multiple entities
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:100',
            'type' => 'nullable|string|in:subjects,teams,projects,users,all',
            'filters' => 'nullable|array',
            'filters.department' => 'nullable|string|in:informatique,mathematiques,physique',
            'filters.status' => 'nullable|string',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        $query = $request->q;
        $type = $request->get('type', 'all');
        $filters = $request->get('filters', []);
        $limit = $request->get('limit', 10);
        $user = $request->user();

        $results = [];
        $total = 0;

        if ($type === 'all' || $type === 'subjects') {
            $subjectResults = $this->searchSubjects($query, $filters, $limit, $user);
            $results['subjects'] = $subjectResults['data'];
            $total += $subjectResults['total'];
        }

        if ($type === 'all' || $type === 'teams') {
            $teamResults = $this->searchTeams($query, $filters, $limit, $user);
            $results['teams'] = $teamResults['data'];
            $total += $teamResults['total'];
        }

        if ($type === 'all' || $type === 'projects') {
            $projectResults = $this->searchProjects($query, $filters, $limit, $user);
            $results['projects'] = $projectResults['data'];
            $total += $projectResults['total'];
        }

        if ($type === 'all' || $type === 'users') {
            $userResults = $this->searchUsers($query, $filters, $limit, $user);
            $results['users'] = $userResults['data'];
            $total += $userResults['total'];
        }

        return response()->json([
            'results' => $results,
            'total' => $total,
            'query' => $query,
            'type' => $type,
            'filters' => $filters
        ]);
    }

    /**
     * Quick search for suggestions/autocomplete
     */
    public function quick(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:1|max:50',
            'type' => 'required|string|in:subjects,teams,projects,users',
            'limit' => 'nullable|integer|min:1|max:20'
        ]);

        $query = $request->q;
        $type = $request->type;
        $limit = $request->get('limit', 5);
        $user = $request->user();

        $results = [];

        switch ($type) {
            case 'subjects':
                $results = Subject::where('title', 'like', "%{$query}%")
                    ->where('status', 'published')
                    ->select('id', 'title', 'supervisor_id')
                    ->with('supervisor:id,first_name,last_name')
                    ->limit($limit)
                    ->get();
                break;

            case 'teams':
                $teamQuery = Team::where('name', 'like', "%{$query}%")
                    ->select('id', 'name', 'status', 'leader_id')
                    ->with('leader:id,first_name,last_name');

                if ($user->hasRole('student')) {
                    $teamQuery->where('status', 'validated');
                }

                $results = $teamQuery->limit($limit)->get();
                break;

            case 'projects':
                $projectQuery = PfeProject::whereHas('subject', function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%");
                })->select('id', 'subject_id', 'team_id', 'status')
                ->with(['subject:id,title', 'team:id,name']);

                if ($user->hasRole('student')) {
                    $projectQuery->whereHas('team.members', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
                } elseif ($user->hasRole('teacher')) {
                    $projectQuery->where('supervisor_id', $user->id);
                }

                $results = $projectQuery->limit($limit)->get();
                break;

            case 'users':
                $userQuery = User::where(function ($q) use ($query) {
                    $q->where('first_name', 'like', "%{$query}%")
                      ->orWhere('last_name', 'like', "%{$query}%")
                      ->orWhere('email', 'like', "%{$query}%");
                })->select('id', 'first_name', 'last_name', 'email', 'department');

                if (!$user->hasRole(['admin_pfe', 'chef_master'])) {
                    $userQuery->where('department', $user->department);
                }

                $results = $userQuery->limit($limit)->get();
                break;
        }

        return response()->json([
            'suggestions' => $results,
            'query' => $query,
            'type' => $type
        ]);
    }

    /**
     * Advanced search with complex filters
     */
    public function advanced(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:subjects,teams,projects',
            'criteria' => 'required|array|min:1',
            'criteria.*.field' => 'required|string',
            'criteria.*.operator' => 'required|string|in:equals,contains,greater_than,less_than,in,between',
            'criteria.*.value' => 'required',
            'sort_by' => 'nullable|string',
            'sort_order' => 'nullable|string|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        $type = $request->type;
        $criteria = $request->criteria;
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $perPage = $request->get('per_page', 15);

        $query = $this->buildAdvancedQuery($type, $criteria);

        if ($query) {
            $results = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);

            return response()->json([
                'data' => $results->items(),
                'meta' => [
                    'current_page' => $results->currentPage(),
                    'total' => $results->total(),
                    'per_page' => $results->perPage(),
                    'last_page' => $results->lastPage()
                ],
                'criteria' => $criteria,
                'sort' => ['by' => $sortBy, 'order' => $sortOrder]
            ]);
        }

        return response()->json([
            'error' => 'Invalid Search',
            'message' => 'Invalid search type or criteria'
        ], 422);
    }

    /**
     * Search subjects
     */
    private function searchSubjects(string $query, array $filters, int $limit, $user): array
    {
        $subjectQuery = Subject::where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%")
              ->orWhereJsonContains('keywords', $query);
        })->with('supervisor:id,first_name,last_name');

        // Apply filters
        if (isset($filters['department'])) {
            $subjectQuery->whereHas('supervisor', function ($q) use ($filters) {
                $q->where('department', $filters['department']);
            });
        }

        if (isset($filters['status'])) {
            $subjectQuery->where('status', $filters['status']);
        } else {
            // Default to published subjects for students
            if ($user->hasRole('student')) {
                $subjectQuery->where('status', 'published');
            }
        }

        $subjects = $subjectQuery->limit($limit)->get();

        return [
            'data' => $subjects,
            'total' => $subjectQuery->count()
        ];
    }

    /**
     * Search teams
     */
    private function searchTeams(string $query, array $filters, int $limit, $user): array
    {
        $teamQuery = Team::where('name', 'like', "%{$query}%")
            ->with(['leader:id,first_name,last_name', 'members.user:id,first_name,last_name']);

        // Apply filters
        if (isset($filters['department'])) {
            $teamQuery->whereHas('leader', function ($q) use ($filters) {
                $q->where('department', $filters['department']);
            });
        }

        if (isset($filters['status'])) {
            $teamQuery->where('status', $filters['status']);
        }

        $teams = $teamQuery->limit($limit)->get();

        return [
            'data' => $teams,
            'total' => $teamQuery->count()
        ];
    }

    /**
     * Search projects
     */
    private function searchProjects(string $query, array $filters, int $limit, $user): array
    {
        $projectQuery = PfeProject::whereHas('subject', function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%");
        })->with(['subject:id,title', 'team:id,name', 'supervisor:id,first_name,last_name']);

        // Apply role-based filtering
        if ($user->hasRole('student')) {
            $projectQuery->whereHas('team.members', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif ($user->hasRole('teacher')) {
            $projectQuery->where('supervisor_id', $user->id);
        }

        // Apply filters
        if (isset($filters['status'])) {
            $projectQuery->where('status', $filters['status']);
        }

        if (isset($filters['department'])) {
            $projectQuery->whereHas('supervisor', function ($q) use ($filters) {
                $q->where('department', $filters['department']);
            });
        }

        $projects = $projectQuery->limit($limit)->get();

        return [
            'data' => $projects,
            'total' => $projectQuery->count()
        ];
    }

    /**
     * Search users
     */
    private function searchUsers(string $query, array $filters, int $limit, $user): array
    {
        $userQuery = User::where(function ($q) use ($query) {
            $q->where('first_name', 'like', "%{$query}%")
              ->orWhere('last_name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('student_id', 'like', "%{$query}%");
        })->select('id', 'first_name', 'last_name', 'email', 'department', 'student_id');

        // Apply role-based filtering
        if (!$user->hasRole(['admin_pfe', 'chef_master'])) {
            $userQuery->where('department', $user->department);
        }

        // Apply filters
        if (isset($filters['department'])) {
            $userQuery->where('department', $filters['department']);
        }

        $users = $userQuery->limit($limit)->get();

        return [
            'data' => $users,
            'total' => $userQuery->count()
        ];
    }

    /**
     * Build advanced search query
     */
    private function buildAdvancedQuery(string $type, array $criteria)
    {
        switch ($type) {
            case 'subjects':
                $query = Subject::with('supervisor:id,first_name,last_name');
                break;
            case 'teams':
                $query = Team::with(['leader:id,first_name,last_name', 'members.user:id,first_name,last_name']);
                break;
            case 'projects':
                $query = PfeProject::with(['subject:id,title', 'team:id,name', 'supervisor:id,first_name,last_name']);
                break;
            default:
                return null;
        }

        foreach ($criteria as $criterion) {
            $field = $criterion['field'];
            $operator = $criterion['operator'];
            $value = $criterion['value'];

            switch ($operator) {
                case 'equals':
                    $query->where($field, $value);
                    break;
                case 'contains':
                    $query->where($field, 'like', "%{$value}%");
                    break;
                case 'greater_than':
                    $query->where($field, '>', $value);
                    break;
                case 'less_than':
                    $query->where($field, '<', $value);
                    break;
                case 'in':
                    $query->whereIn($field, (array) $value);
                    break;
                case 'between':
                    if (is_array($value) && count($value) === 2) {
                        $query->whereBetween($field, $value);
                    }
                    break;
            }
        }

        return $query;
    }
}