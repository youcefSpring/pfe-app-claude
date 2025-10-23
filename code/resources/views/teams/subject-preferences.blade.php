@extends('layouts.pfe-app')

@section('page-title', __('app.subject_preferences'))

@section('content')
    <div class="container-fluid">
        <!-- Team Info Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="card-title mb-1">
                                    <i class="fas fa-list-ol"></i> {{ __('app.subject_preferences_for_team') }}: {{ $team->name }}
                                </h4>
                                <small class="text-muted">{{ __('app.manage_your_team_subject_preferences') }}</small>
                            </div>
                            <a href="{{ route('teams.my-team') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> {{ __('app.back_to_team') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Team Members -->
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-users me-2 text-primary"></i>
                            <span class="fw-semibold me-2">{{ __('app.members') }}:</span>
                            @foreach($team->members as $member)
                                <span class="badge bg-light text-dark me-2">
                                    {{ $member->user->name }}
                                    @if($member->role === 'leader')
                                        <span class="text-primary">({{ __('app.leader') }})</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>

                        @php
                            $canManage = $team->canManagePreferences() &&
                                       auth()->user()->teamMember &&
                                       auth()->user()->teamMember->team_id == $team->id;
                                       dd([
                                        'canManage' => $canManage,
                                        'canManageCheck' => $team->canManagePreferences(),
                                        'authTeamMember' => auth()->user()->teamMember->team_id == $team->id,
                                        'has_team' => auth()->user()->teamMember ,
                                       ])
                                    //    dd($canManage);
                        @endphp

                        @if(!$canManage)
                            <div class="alert alert-warning">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ __('app.cannot_modify_preferences_reason') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Current Preferences Column -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list"></i> {{ __('app.selected_subjects') }}
                            @php
                                $currentCount = isset($currentPreferences) ? $currentPreferences->count() : 0;
                                $maxSubjects = 10;
                                $percentage = ($currentCount / $maxSubjects) * 100;
                            @endphp
                            <span class="badge @if($currentCount >= $maxSubjects) bg-success @elseif($currentCount >= 7) bg-warning @else bg-primary @endif">
                                {{ $currentCount }}/{{ $maxSubjects }}
                            </span>
                        </h5>
                        <div class="mb-2">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar @if($currentCount >= $maxSubjects) bg-success @elseif($currentCount >= 7) bg-warning @else bg-primary @endif"
                                     role="progressbar" style="width: {{ $percentage }}%"
                                     aria-valuenow="{{ $currentCount }}"
                                     aria-valuemin="0"
                                     aria-valuemax="{{ $maxSubjects }}"></div>
                            </div>
                            <small class="text-muted">{{ __('app.subjects_selected_progress', ['current' => $currentCount, 'max' => $maxSubjects]) }}</small>
                        </div>
                        <small class="text-muted">{{ __('app.ordered_by_preference_order') }}</small>
                    </div>
                    <div class="card-body">
                        @if(!isset($currentPreferences) || $currentPreferences->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">{{ __('app.no_subjects_selected_yet') }}</h6>
                                <p class="text-muted small">{{ __('app.start_by_adding_subjects_from_available_list') }}</p>
                            </div>
                        @else
                            <div id="preference-list">
                                @foreach($currentPreferences as $preference)
                                    <div class="card mb-2 preference-item @if($preference->is_allocated) border-success @endif"
                                         data-subject-id="{{ $preference->subject_id }}"
                                         data-is-allocated="{{ $preference->is_allocated ? 'true' : 'false' }}">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="d-flex align-items-start flex-grow-1">
                                                    @php
                                                        $badgeClass = 'bg-secondary';
                                                        if ($preference->is_allocated) {
                                                            $badgeClass = 'bg-success';
                                                        } elseif ($preference->preference_order <= 3) {
                                                            $badgeClass = 'bg-success'; // Green for top 3
                                                        } elseif ($preference->preference_order >= 8) {
                                                            $badgeClass = 'bg-warning'; // Orange for bottom 3
                                                        } else {
                                                            $badgeClass = 'bg-primary'; // Blue for middle
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }} me-3 fs-6" style="width: 30px; height: 30px; line-height: 30px;">
                                                        {{ $preference->preference_order }}
                                                    </span>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $preference->subject->title }}</h6>
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-chalkboard-teacher"></i>
                                                            {{ __('app.teacher') }}: {{ $preference->subject->teacher->name ?? __('app.not_assigned') }}
                                                        </small>
                                                        <small class="text-info d-block">
                                                            <i class="fas fa-calendar-alt"></i>
                                                            {{ __('app.submitted_on') }}: {{ $preference->selected_at ? $preference->selected_at->format('d/m/Y H:i') : __('app.not_available') }}
                                                        </small>
                                                        @if($preference->is_allocated)
                                                            <span class="badge bg-success mt-1">
                                                                <i class="fas fa-check"></i> {{ __('app.allocated') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($canManage && !$preference->is_allocated)
                                                    <div class="btn-group shadow-sm">
                                                        <div class="btn-group-vertical me-2">
                                                            <button class="btn btn-primary btn-sm fw-bold btn-enhanced move-up @if($loop->first) disabled @endif"
                                                                    type="button" @if($loop->first) disabled @endif
                                                                    title="{{ __('app.move_up') }}"
                                                                    style="min-width: 45px; padding: 8px;">
                                                                <i class="fas fa-chevron-up text-white"></i>
                                                            </button>
                                                            <button class="btn btn-primary btn-sm fw-bold btn-enhanced move-down @if($loop->last) disabled @endif"
                                                                    type="button" @if($loop->last) disabled @endif
                                                                    title="{{ __('app.move_down') }}"
                                                                    style="min-width: 45px; padding: 8px;">
                                                                <i class="fas fa-chevron-down text-white"></i>
                                                            </button>
                                                        </div>
                                                        <form action="{{ route('teams.remove-subject-preference', [$team, $preference->subject]) }}"
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger fw-bold btn-enhanced"
                                                                    onclick="return showDeleteConfirmation({
                                                                        itemName: '{{ $preference->subject->title }}',
                                                                        message: '{{ __('app.confirm_remove_preference') }}',
                                                                        form: this.closest('form')
                                                                    })"
                                                                    title="{{ __('app.remove_preference') }}"
                                                                    style="min-width: 40px; padding: 8px;">
                                                                <i class="fas fa-trash text-white"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($canManage && isset($currentPreferences) && $currentPreferences->where('is_allocated', false)->count() > 1)
                                <form id="update-order-form" action="{{ route('teams.update-preference-order', $team) }}" method="POST" class="mt-3">
                                    @csrf
                                    @method('PUT')
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary fw-bold shadow btn-enhanced">
                                            <i class="fas fa-save text-white"></i> {{ __('app.save_preference_order') }}
                                        </button>
                                    </div>
                                </form>
                            @endif

                        @endif
                    </div>
                </div>
            </div>

            <!-- Available Subjects Column -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle"></i> {{ __('app.available_subjects') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($canManage && (!isset($currentPreferences) || $currentPreferences->count() < 10))
                            <!-- Search Filter -->
                            <div class="mb-3">
                                <input type="text" id="subject-search" class="form-control"
                                       placeholder="{{ __('app.search_subjects') }}...">
                            </div>

                            <div style="max-height: 500px; overflow-y: auto;" id="available-subjects">
                                @foreach($availableSubjects as $subject)
                                    <div class="card mb-2 subject-item">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 subject-title">{{ $subject->title }}</h6>
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-chalkboard-teacher"></i>
                                                        {{ __('app.teacher') }}: {{ $subject->teacher->name ?? __('app.not_assigned') }}
                                                    </small>
                                                    @if($subject->description)
                                                        <small class="text-muted d-block mt-1">{{ Str::limit($subject->description, 120) }}</small>
                                                    @endif
                                                    <small class="text-info d-block mt-1">
                                                        <i class="fas fa-graduation-cap"></i>
                                                        {{ ucfirst($subject->target_grade) }}
                                                    </small>
                                                </div>

                                                <form action="{{ route('teams.add-subject-preference', $team) }}" method="POST" class="ms-2">
                                                    @csrf
                                                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                                    <button type="submit" class="btn btn-success fw-bold btn-enhanced">
                                                        <i class="fas fa-plus text-white"></i> {{ __('app.add') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($availableSubjects->isEmpty())
                                <div class="text-center py-5">
                                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">{{ __('app.no_available_subjects') }}</h6>
                                    <p class="text-muted small">{{ __('app.all_subjects_already_selected') }}</p>
                                </div>
                            @endif
                        @else
                            @if(isset($currentPreferences) && $currentPreferences->count() >= 10)
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    {{ __('app.maximum_subjects_reached') }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="row">
            <div class="col-12">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> {{ __('app.instructions') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('app.select_up_to_10_subjects') }}</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('app.order_by_preference') }}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('app.subject_allocation_based_on_preference') }}</li>
                                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i> {{ __('app.cannot_change_after_allocation') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
    /* Ensure Font Awesome loads properly */
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');

    .btn-enhanced {
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-enhanced:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3) !important;
        border: none !important;
        color: white !important;
    }

    .btn-success {
        background: linear-gradient(45deg, #28a745, #1e7e34) !important;
        border: none !important;
        color: white !important;
    }

    .btn-danger {
        background: linear-gradient(45deg, #dc3545, #c82333) !important;
        border: none !important;
        color: white !important;
    }

    .btn-warning {
        background: linear-gradient(45deg, #ffc107, #e0a800) !important;
        border: none !important;
        color: #212529 !important;
    }

    /* Fallback for Font Awesome icons */
    .fa-chevron-up::before {
        content: "↑";
        font-family: Arial, sans-serif;
        font-weight: bold;
    }

    .fa-chevron-down::before {
        content: "↓";
        font-family: Arial, sans-serif;
        font-weight: bold;
    }

    .fa-trash::before {
        content: "🗑";
        font-family: Arial, sans-serif;
    }

    .fa-plus::before {
        content: "+";
        font-family: Arial, sans-serif;
        font-weight: bold;
        font-size: 1.2em;
    }

    .fa-save::before {
        content: "💾";
        font-family: Arial, sans-serif;
    }


    .fa-list-ol::before {
        content: "📋";
        font-family: Arial, sans-serif;
    }

    .fa-arrow-left::before {
        content: "←";
        font-family: Arial, sans-serif;
        font-weight: bold;
    }

    .fa-users::before {
        content: "👥";
        font-family: Arial, sans-serif;
    }

    .fa-info-circle::before {
        content: "ℹ";
        font-family: Arial, sans-serif;
        font-weight: bold;
    }

    .fa-list::before {
        content: "📄";
        font-family: Arial, sans-serif;
    }

    .fa-inbox::before {
        content: "📥";
        font-family: Arial, sans-serif;
    }

    .fa-chalkboard-teacher::before {
        content: "👨‍🏫";
        font-family: Arial, sans-serif;
    }

    .fa-calendar-alt::before {
        content: "📅";
        font-family: Arial, sans-serif;
    }

    .fa-check::before {
        content: "✓";
        font-family: Arial, sans-serif;
        font-weight: bold;
        color: green;
    }

    .fa-plus-circle::before {
        content: "➕";
        font-family: Arial, sans-serif;
    }

    .fa-graduation-cap::before {
        content: "🎓";
        font-family: Arial, sans-serif;
    }

    .fa-search::before {
        content: "🔍";
        font-family: Arial, sans-serif;
    }

    /* Force content visibility */
    .card-body, .card-header, .btn {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .preference-item {
        transition: all 0.3s ease;
    }

    .preference-item:hover {
        transform: translateX(2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .subject-item {
        transition: all 0.3s ease;
    }

    .subject-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    </style>
    @endpush

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('subject-search');
        const subjectItems = document.querySelectorAll('.subject-item');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                subjectItems.forEach(item => {
                    const title = item.querySelector('.subject-title').textContent.toLowerCase();
                    if (title.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }

        // Reorder functionality
        const updateOrderForm = document.getElementById('update-order-form');

        function swapPreferences(button, direction) {
            const currentItem = button.closest('.preference-item');
            const siblingItem = direction === 'up'
                ? currentItem.previousElementSibling
                : currentItem.nextElementSibling;

            if (siblingItem && siblingItem.classList.contains('preference-item')) {
                if (direction === 'up') {
                    currentItem.parentNode.insertBefore(currentItem, siblingItem);
                } else {
                    currentItem.parentNode.insertBefore(siblingItem, currentItem);
                }
                updateOrderNumbers();
                updateButtons();
            }
        }

        function updateOrderNumbers() {
            const items = document.querySelectorAll('.preference-item');
            items.forEach((item, index) => {
                const orderBadge = item.querySelector('.badge');
                if (orderBadge) {
                    const newOrder = index + 1;
                    const isAllocated = item.dataset.isAllocated === 'true';

                    // Always update the badge text (number)
                    const oldOrder = orderBadge.textContent;
                    orderBadge.textContent = newOrder;

                    // Add a brief highlight animation if the order changed
                    if (oldOrder !== newOrder.toString()) {
                        orderBadge.style.transform = 'scale(1.2)';
                        orderBadge.style.transition = 'transform 0.2s ease';
                        setTimeout(() => {
                            orderBadge.style.transform = 'scale(1)';
                        }, 200);
                    }

                    // Update badge color based on new position (only for non-allocated preferences)
                    if (!isAllocated) {
                        // Remove all existing badge color classes
                        orderBadge.classList.remove('bg-success', 'bg-primary', 'bg-warning', 'bg-secondary');

                        // Add new color class based on position
                        let newBadgeClass = 'bg-secondary';
                        if (newOrder <= 3) {
                            newBadgeClass = 'bg-success'; // Green for top 3
                        } else if (newOrder >= 8) {
                            newBadgeClass = 'bg-warning'; // Orange for bottom 3
                        } else {
                            newBadgeClass = 'bg-primary'; // Blue for middle
                        }

                        orderBadge.classList.add(newBadgeClass);
                    }
                }
            });
        }

        function updateButtons() {
            const items = document.querySelectorAll('.preference-item');
            items.forEach((item, index) => {
                const upBtn = item.querySelector('.move-up');
                const downBtn = item.querySelector('.move-down');

                if (upBtn) {
                    upBtn.disabled = index === 0;
                    upBtn.classList.toggle('disabled', index === 0);
                }
                if (downBtn) {
                    downBtn.disabled = index === items.length - 1;
                    downBtn.classList.toggle('disabled', index === items.length - 1);
                }
            });
        }

        // Use event delegation for move buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.move-up')) {
                e.preventDefault();
                const button = e.target.closest('.move-up');
                if (!button.disabled) {
                    swapPreferences(button, 'up');
                }
            } else if (e.target.closest('.move-down')) {
                e.preventDefault();
                const button = e.target.closest('.move-down');
                if (!button.disabled) {
                    swapPreferences(button, 'down');
                }
            }
        });

        // Update order form submission
        if (updateOrderForm) {
            updateOrderForm.addEventListener('submit', function(e) {
                // Clear existing hidden inputs
                const existingInputs = updateOrderForm.querySelectorAll('input[name="subject_ids[]"]');
                existingInputs.forEach(input => input.remove());

                // Get current order of preferences
                const items = document.querySelectorAll('.preference-item');
                console.log('Found items:', items.length);

                // Create hidden inputs for each subject ID in the correct order
                items.forEach((item, index) => {
                    const subjectId = item.dataset.subjectId;
                    console.log(`Item ${index + 1}: Subject ID ${subjectId}`);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'subject_ids[]';
                    hiddenInput.value = subjectId;
                    updateOrderForm.appendChild(hiddenInput);
                });

                console.log('Form data being submitted:', new FormData(updateOrderForm));
            });
        }

        // Initialize button states
        updateButtons();
    });

    </script>
    @endpush
@endsection
