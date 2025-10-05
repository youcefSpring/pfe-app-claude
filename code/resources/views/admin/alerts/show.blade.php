@extends('layouts.pfe-app')

@section('page-title', __('app.alert_details'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.alert_details') }}</h4>
                    <small class="text-muted">{{ __('app.view_and_respond_to_alert') }}</small>
                </div>
                <div class="card-body">
                    <!-- Student Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('app.student_information') }}</h6>
                            <p class="mb-1"><strong>{{ __('app.name') }}:</strong> {{ $alert->student->name }}</p>
                            @if($alert->student->matricule)
                                <p class="mb-1"><strong>{{ __('app.matricule') }}:</strong> {{ $alert->student->matricule }}</p>
                            @endif
                            @if($alert->student->email)
                                <p class="mb-1"><strong>{{ __('app.email') }}:</strong> {{ $alert->student->email }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">{{ __('app.alert_information') }}</h6>
                            <p class="mb-1"><strong>{{ __('app.sent_at') }}:</strong> {{ $alert->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mb-1"><strong>{{ __('app.status') }}:</strong>
                                @if($alert->status === 'pending')
                                    <span class="badge bg-warning">{{ __('app.pending') }}</span>
                                @else
                                    <span class="badge bg-success">{{ __('app.responded') }}</span>
                                @endif
                            </p>
                            @if($alert->status === 'responded' && $alert->respondedBy)
                                <p class="mb-1"><strong>{{ __('app.responded_by') }}:</strong> {{ $alert->respondedBy->name }}</p>
                                <p class="mb-1"><strong>{{ __('app.responded_at') }}:</strong> {{ $alert->responded_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Student Message -->
                    <div class="mb-4">
                        <h6 class="text-muted">{{ __('app.student_message') }}</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0">{{ $alert->message }}</p>
                        </div>
                    </div>

                    <!-- Admin Response -->
                    @if($alert->admin_response)
                        <div class="mb-4">
                            <h6 class="text-muted">{{ __('app.admin_response') }}</h6>
                            <div class="p-3 bg-primary bg-opacity-10 rounded">
                                <p class="mb-0">{{ $alert->admin_response }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Response Form -->
                    @if($alert->status === 'pending')
                        <div class="border-top pt-4">
                            <h6 class="text-muted mb-3">{{ __('app.send_response') }}</h6>
                            <form action="{{ route('admin.alerts.respond', $alert->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="admin_response" class="form-label">{{ __('app.your_response') }} <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('admin_response') is-invalid @enderror"
                                              id="admin_response" name="admin_response" rows="4"
                                              placeholder="{{ __('app.type_your_response') }}" required>{{ old('admin_response') }}</textarea>
                                    @error('admin_response')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.alerts') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> {{ __('app.back_to_alerts') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> {{ __('app.send_response') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="d-flex justify-content-start">
                            <a href="{{ route('admin.alerts') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('app.back_to_alerts') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection