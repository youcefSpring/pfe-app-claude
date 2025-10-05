@extends('layouts.pfe-app')

@section('page-title', __('app.alert_details'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.alert_details') }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6>{{ __('app.original_message') }}</h6>
                        <div class="border p-3 bg-light rounded">
                            {{ $alert->message }}
                        </div>
                    </div>

                    @if($alert->admin_response)
                        <div class="mb-4">
                            <h6>{{ __('app.admin_response') }}</h6>
                            <div class="border p-3 bg-success bg-opacity-10 rounded">
                                {{ $alert->admin_response }}
                            </div>
                            <small class="text-muted">
                                {{ __('app.responded_by') }}: {{ $alert->respondedBy->name }}
                                ({{ $alert->responded_at->format('M d, Y H:i') }})
                            </small>
                        </div>
                    @else
                        @if($alert->status === 'pending')
                            <div class="mb-4">
                                <h6>{{ __('app.respond_to_alert') }}</h6>
                                <form action="{{ route('admin.alerts.respond', $alert) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="form-control @error('admin_response') is-invalid @enderror"
                                                  name="admin_response" rows="4"
                                                  placeholder="{{ __('app.type_your_response') }}" required></textarea>
                                        @error('admin_response')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> {{ __('app.send_response') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">{{ __('app.alert_information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">{{ __('app.student') }}</small>
                        <div>
                            <strong>{{ $alert->student->name }}</strong>
                            <br><small>{{ $alert->student->email }}</small>
                            @if($alert->student->matricule)
                                <br><small>{{ __('app.matricule') }}: {{ $alert->student->matricule }}</small>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">{{ __('app.status') }}</small>
                        <div>
                            @if($alert->status === 'pending')
                                <span class="badge bg-warning">{{ __('app.pending') }}</span>
                            @else
                                <span class="badge bg-success">{{ __('app.responded') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">{{ __('app.sent_at') }}</small>
                        <div>{{ $alert->created_at->format('M d, Y H:i') }}</div>
                        <small class="text-muted">{{ $alert->created_at->diffForHumans() }}</small>
                    </div>

                    @if($alert->responded_at)
                        <div class="mb-3">
                            <small class="text-muted">{{ __('app.responded_at') }}</small>
                            <div>{{ $alert->responded_at->format('M d, Y H:i') }}</div>
                            <small class="text-muted">{{ $alert->responded_at->diffForHumans() }}</small>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">{{ __('app.responded_by') }}</small>
                            <div>{{ $alert->respondedBy->name }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <a href="{{ route('admin.alerts') }}" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-left"></i> {{ __('app.back_to_alerts') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection