@extends('layouts.pfe-app')

@section('page-title', __('app.create_academic_year'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('app.create_academic_year') }}</h4>
                    <small class="text-muted">{{ __('app.create_new_academic_year_description') }}</small>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> {{ __('app.please_fix_errors') }}</h6>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.academic-years.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="year" class="form-label">{{ __('app.academic_year') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('year') is-invalid @enderror"
                                           id="year" name="year" value="{{ old('year') }}"
                                           placeholder="2024-2025" required>
                                    @error('year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">{{ __('app.year_format_example') }}</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">{{ __('app.title') }}</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title') }}"
                                           placeholder="{{ __('app.academic_year_title_placeholder') }}">
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">{{ __('app.start_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">{{ __('app.end_date') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">{{ __('app.description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3"
                                      placeholder="{{ __('app.academic_year_description_placeholder') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('is_current') is-invalid @enderror"
                                       type="checkbox" id="is_current" name="is_current" value="1"
                                       {{ old('is_current') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_current">
                                    {{ __('app.set_as_current_year') }}
                                </label>
                                @error('is_current')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted d-block">{{ __('app.current_year_warning') }}</small>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.academic-years.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('app.back') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('app.create_academic_year') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const yearInput = document.getElementById('year');
    const titleInput = document.getElementById('title');

    function updateYearFromDates() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && startDate < endDate) {
            const startYear = startDate.getFullYear();
            const endYear = endDate.getFullYear();
            const yearString = `${startYear}-${endYear}`;

            if (!yearInput.value) {
                yearInput.value = yearString;
            }

            if (!titleInput.value) {
                titleInput.value = `Academic Year ${yearString}`;
            }
        }
    }

    startDateInput.addEventListener('change', updateYearFromDates);
    endDateInput.addEventListener('change', updateYearFromDates);
});
</script>
@endsection