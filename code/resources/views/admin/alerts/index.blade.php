@extends('layouts.pfe-app')

@section('page-title', __('app.student_alerts'))

@section('content')
<div class="container-fluid">
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-0">{{ __('app.student_alerts') }}</h4>
                        <small class="text-muted">{{ __('app.manage_student_communications') }}</small>
                    </div>
                    <div>
                        <span class="badge bg-warning">{{ $alerts->where('status', 'pending')->count() }} {{ __('app.pending') }}</span>
                        <span class="badge bg-success">{{ $alerts->where('status', 'responded')->count() }} {{ __('app.responded') }}</span>
                    </div>
                </div>

                <div class="card-body">
                    @if($alerts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('app.student') }}</th>
                                        <th>{{ __('app.message') }}</th>
                                        <th>{{ __('app.date') }}</th>
                                        <th>{{ __('app.status') }}</th>
                                        <th>{{ __('app.responded_by') }}</th>
                                        <th>{{ __('app.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alerts as $alert)
                                        <tr class="{{ $alert->status === 'pending' ? 'table-warning' : '' }}">
                                            <td>
                                                <div>
                                                    <strong>{{ $alert->student->name }}</strong>
                                                    @if($alert->student->matricule)
                                                        <br><small class="text-muted">({{ $alert->student->matricule }})</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <p class="mb-1">{{ Str::limit($alert->message, 100) }}</p>
                                                @if(strlen($alert->message) > 100)
                                                    <small class="text-muted">{{ __('app.click_to_view_full') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $alert->created_at->format('d/m/Y H:i') }}</small>
                                                <br><small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                                            </td>
                                            <td>
                                                @if($alert->status === 'pending')
                                                    <span class="badge bg-warning">{{ __('app.pending') }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ __('app.responded') }}</span>
                                                    @if($alert->responded_at)
                                                        <br><small class="text-muted">{{ $alert->responded_at->format('d/m/Y H:i') }}</small>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if($alert->respondedBy)
                                                    {{ $alert->respondedBy->name }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.alerts.show', $alert->id) }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> {{ __('app.view') }}
                                                    </a>
                                                    @if($alert->status === 'pending')
                                                        <a href="{{ route('admin.alerts.show', $alert->id) }}"
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fas fa-reply"></i> {{ __('app.respond') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $alerts->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bell fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">{{ __('app.no_alerts_yet') }}</h5>
                            <p class="text-muted">{{ __('app.students_can_send_alerts') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection