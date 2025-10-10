@extends('layouts.pfe-app')

@section('title', __('app.edit_speciality'))

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('app.edit_speciality') }}: {{ $speciality->name }}</h5>
                        <div class="btn-group">
                            <a href="{{ route('specialities.show', $speciality) }}" class="btn btn-outline-info btn-sm">
                                <i class="bi bi-eye"></i> {{ __('app.view') }}
                            </a>
                            <a href="{{ route('specialities.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-arrow-left"></i> {{ __('app.back') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('specialities.update', $speciality) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">{{ __('app.name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name', $speciality->name) }}" required
                                               placeholder="{{ __('app.speciality_name_placeholder') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="code" class="form-label">{{ __('app.code') }}</label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                                               id="code" name="code" value="{{ old('code', $speciality->code) }}"
                                               placeholder="{{ __('app.speciality_code_placeholder') }}">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="level" class="form-label">{{ __('app.level') }} <span class="text-danger">*</span></label>
                                        <select class="form-select @error('level') is-invalid @enderror" id="level" name="level" required>
                                            <option value="">{{ __('app.select_level') }}</option>
                                            <option value="licence" {{ old('level', $speciality->level) === 'licence' ? 'selected' : '' }}>{{ __('app.licence') }}</option>
                                            <option value="master" {{ old('level', $speciality->level) === 'master' ? 'selected' : '' }}>{{ __('app.master') }}</option>
                                            <option value="ingenieur" {{ old('level', $speciality->level) === 'ingenieur' ? 'selected' : '' }}>{{ __('app.ingenieur') }}</option>
                                        </select>
                                        @error('level')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="academic_year" class="form-label">{{ __('app.academic_year') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('academic_year') is-invalid @enderror"
                                               id="academic_year" name="academic_year"
                                               value="{{ old('academic_year', $speciality->academic_year) }}"
                                               required placeholder="2024/2025">
                                        @error('academic_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="semester" class="form-label">{{ __('app.semester') }}</label>
                                        <input type="text" class="form-control @error('semester') is-invalid @enderror"
                                               id="semester" name="semester" value="{{ old('semester', $speciality->semester) }}"
                                               placeholder="{{ __('app.semester_placeholder') }}">
                                        @error('semester')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">{{ __('app.description') }}</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4"
                                          placeholder="{{ __('app.speciality_description_placeholder') }}">{{ old('description', $speciality->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                           value="1" {{ old('is_active', $speciality->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        {{ __('app.active') }}
                                    </label>
                                </div>
                                <small class="form-text text-muted">{{ __('app.active_speciality_help') }}</small>
                            </div>

                            @if($speciality->users()->count() > 0)
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    {{ __('app.speciality_has_users_warning', ['count' => $speciality->users()->count()]) }}
                                </div>
                            @endif

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('specialities.index') }}" class="btn btn-secondary">
                                    {{ __('app.cancel') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> {{ __('app.update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Danger Zone -->
                @if($speciality->users()->count() === 0)
                    <div class="card mt-4 border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">{{ __('app.danger_zone') }}</h6>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">{{ __('app.delete_speciality_warning') }}</p>
                            <form action="{{ route('specialities.destroy', $speciality) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ __('app.confirm_delete_speciality') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash"></i> {{ __('app.delete_speciality') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection