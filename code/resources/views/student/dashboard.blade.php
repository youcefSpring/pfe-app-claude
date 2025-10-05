@extends('layouts.pfe-app')

@section('page-title', __('app.student_dashboard'))

@section('content')
<div class="container-fluid">
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Quick Actions for Students -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('app.quick_actions') }}</h5>

                    @php
                        $hasScheduledDefense = \App\Models\Defense::whereHas('project.team.members', function($q) {
                            $q->where('student_id', auth()->id());
                        })->exists();
                    @endphp

                    <div class="row">
                        <div class="{{ $hasScheduledDefense ? 'col-md-3' : 'col-md-4' }}">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-primary w-100 mb-2">
                                <i class="fas fa-tachometer-alt"></i> {{ __('app.main_dashboard') }}
                            </a>
                        </div>
                        <div class="{{ $hasScheduledDefense ? 'col-md-3' : 'col-md-4' }}">
                            <a href="{{ route('subjects.index') }}" class="btn btn-outline-info w-100 mb-2">
                                <i class="fas fa-book"></i> {{ __('app.subjects') }}
                            </a>
                        </div>
                        <div class="{{ $hasScheduledDefense ? 'col-md-3' : 'col-md-4' }}">
                            <a href="{{ route('teams.my-team') }}" class="btn btn-outline-success w-100 mb-2">
                                <i class="fas fa-users"></i> {{ __('app.my_team') }}
                            </a>
                        </div>
                        @if($hasScheduledDefense)
                        <div class="col-md-3">
                            <a href="{{ route('defenses.my-defense') }}" class="btn btn-outline-warning w-100 mb-2">
                                <i class="fas fa-shield-alt"></i> {{ __('app.my_defense') }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- My Marks Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.my_marks') }}</h4>
                    <small class="text-muted">{{ __('app.view_your_academic_performance') }}</small>
                </div>
                <div class="card-body">
                    @if($marks->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.date') }}</th>
                                        <th>{{ __('app.mark') }} 1</th>
                                        <th>{{ __('app.mark') }} 2</th>
                                        <th>{{ __('app.mark') }} 3</th>
                                        <th>{{ __('app.mark') }} 4</th>
                                        <th>{{ __('app.mark') }} 5</th>
                                        <th>{{ __('app.average') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($marks as $mark)
                                        <tr>
                                            <td>{{ $mark->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                @if($mark->mark_1)
                                                    <span class="badge bg-primary">{{ $mark->mark_1 }}/20</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mark->mark_2)
                                                    <span class="badge bg-primary">{{ $mark->mark_2 }}/20</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mark->mark_3)
                                                    <span class="badge bg-primary">{{ $mark->mark_3 }}/20</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mark->mark_4)
                                                    <span class="badge bg-primary">{{ $mark->mark_4 }}/20</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mark->mark_5)
                                                    <span class="badge bg-primary">{{ $mark->mark_5 }}/20</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $total = 0;
                                                    $count = 0;
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        $markField = "mark_$i";
                                                        if ($mark->$markField) {
                                                            $total += $mark->$markField;
                                                            $count++;
                                                        }
                                                    }
                                                    $average = $count > 0 ? round($total / $count, 2) : 0;
                                                @endphp
                                                @if($average >= 16)
                                                    <span class="badge bg-success">{{ $average }}/20</span>
                                                @elseif($average >= 12)
                                                    <span class="badge bg-warning">{{ $average }}/20</span>
                                                @elseif($average >= 10)
                                                    <span class="badge bg-info">{{ $average }}/20</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $average }}/20</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('app.no_marks_yet') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Send Alert Section -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.send_alert_to_admin') }}</h4>
                    <small class="text-muted">{{ __('app.contact_administration') }}</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('student.alert.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="message" class="form-label">{{ __('app.message') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror"
                                      id="message" name="message" rows="4"
                                      placeholder="{{ __('app.type_your_message') }}" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> {{ __('app.send_alert') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- My Alerts Section -->
            <div class="card mt-3">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.my_alerts') }}</h4>
                    <small class="text-muted">{{ __('app.track_your_messages') }}</small>
                </div>
                <div class="card-body">
                    @if($alerts->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($alerts->take(5) as $alert)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <p class="mb-1 small">{{ Str::limit($alert->message, 50) }}</p>
                                            <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if($alert->status === 'responded')
                                            <span class="badge bg-success">{{ __('app.responded') }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ __('app.pending') }}</span>
                                        @endif
                                    </div>
                                    @if($alert->admin_response)
                                        <div class="mt-2 p-2 bg-light rounded">
                                            <small><strong>{{ __('app.admin_response') }}:</strong> {{ $alert->admin_response }}</small>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-bell fa-2x text-muted mb-2"></i>
                            <p class="text-muted small">{{ __('app.no_alerts_sent') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection