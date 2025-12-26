<div class="container-fluid p-0">
    <div class="mb-3">
        <h6 class="mb-2">{{ $subject->title }}</h6>
        <small class="text-muted">Teams that requested this subject ({{ $teamRequests->count() }} total)</small>
    </div>

    @if($teamRequests->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th width="80">Priority</th>
                        <th>Team Name</th>
                        <th>Members</th>
                        <th width="120">Request Date</th>
                        @if(auth()->user()->role === 'admin')
                            <th width="100">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($teamRequests as $request)
                        <tr>
                            <td>
                                <span class="badge bg-{{ $request->preference_order == 1 ? 'success' : ($request->preference_order == 2 ? 'warning' : 'secondary') }}">
                                    {{ $request->preference_order }}{{ $request->preference_order == 1 ? 'st' : ($request->preference_order == 2 ? 'nd' : ($request->preference_order == 3 ? 'rd' : 'th')) }}
                                </span>
                                @if($request->preference_order == 1)
                                    <i class="bi bi-star-fill text-warning ms-1" title="First choice"></i>
                                @endif
                            </td>
                            <td>
                                @if(isset($request->team))
                                    {{-- Team request --}}
                                    <div class="fw-semibold">{{ $request->team->name }}</div>
                                    @if($request->team->description)
                                        <small class="text-muted">{{ Str::limit($request->team->description, 50) }}</small>
                                    @endif
                                @else
                                    {{-- Individual student request --}}
                                    <div class="fw-semibold">{{ $request->student->name }}</div>
                                    <small class="text-muted">Individual Student</small>
                                @endif
                            </td>
                            <td>
                                @if(isset($request->team))
                                    {{-- Team members --}}
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($request->team->members->take(3) as $member)
                                            <span class="badge bg-light text-dark">{{ $member->user->name }}</span>
                                        @endforeach
                                        @if($request->team->members->count() > 3)
                                            <span class="badge bg-secondary">+{{ $request->team->members->count() - 3 }} more</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $request->team->members->count() }} member{{ $request->team->members->count() > 1 ? 's' : '' }} total</small>
                                @else
                                    {{-- Individual student --}}
                                    <span class="badge bg-warning">Individual</span>
                                    <br><small class="text-muted">No team assigned</small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $request->created_at->format('M d, Y') }}</small>
                            </td>
                            @if(auth()->user()->role === 'admin')
                                <td>
                                    @if(isset($request->team) && !$request->team->project)
                                        <form action="{{ route('admin.subjects.assign-team', $subject) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="team_id" value="{{ $request->team->id }}">
                                            <button type="submit"
                                                    class="btn btn-sm btn-success"
                                                    title="{{ __('app.assign_to_team') }}"
                                                    onclick="return confirm('{{ __('app.confirm_assign_subject_to_team', ['team' => $request->team->name]) }}')">
                                                <i class="bi bi-check-circle"></i> {{ __('app.assign') }}
                                            </button>
                                        </form>
                                    @elseif(isset($request->team) && $request->team->project)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle-fill"></i> {{ __('app.assigned') }}
                                        </span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Priority Summary -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body py-2">
                        <h6 class="card-title mb-2">Priority Summary</h6>
                        <div class="row text-center">
                            <div class="col">
                                <span class="badge bg-success fs-6">{{ $teamRequests->where('preference_order', 1)->count() }}</span>
                                <br><small class="text-muted">1st Choice</small>
                            </div>
                            <div class="col">
                                <span class="badge bg-warning fs-6">{{ $teamRequests->where('preference_order', 2)->count() }}</span>
                                <br><small class="text-muted">2nd Choice</small>
                            </div>
                            <div class="col">
                                <span class="badge bg-secondary fs-6">{{ $teamRequests->where('preference_order', 3)->count() }}</span>
                                <br><small class="text-muted">3rd Choice</small>
                            </div>
                            <div class="col">
                                <span class="badge bg-info fs-6">{{ $teamRequests->where('preference_order', '>', 3)->count() }}</span>
                                <br><small class="text-muted">4th+ Choice</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-4">
            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 text-muted">{{ __('app.no_team_requests') }}</h5>
            <p class="text-muted">{{ __('app.no_teams_requested_subjects') }}</p>
        </div>
    @endif
</div>