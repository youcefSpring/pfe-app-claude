@extends('layouts.app')

@section('title', __('app.subject_preferences'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Team Info -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold text-gray-900">{{ __('app.subject_preferences_for_team') }}: {{ $team->name }}</h1>
                <a href="{{ route('teams.show', $team) }}" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-1"></i> {{ __('app.back_to_team') }}
                </a>
            </div>

            <!-- Team Members -->
            <div class="flex items-center text-sm text-gray-600">
                <i class="fas fa-users mr-2"></i>
                <span>{{ __('app.members') }}:</span>
                @foreach($team->members as $member)
                    <span class="ml-2 font-medium">
                        {{ $member->user->name }}
                        @if($member->role === 'leader')
                            <span class="text-xs text-blue-600">({{ __('app.leader') }})</span>
                        @endif
                    </span>
                @endforeach
            </div>

            @if(!$canManage)
                <div class="mt-4 bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded">
                    <p class="text-sm">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ __('app.cannot_modify_preferences_reason') }}
                    </p>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Current Preferences -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    {{ __('app.selected_subjects') }}
                    <span class="text-sm text-gray-500">({{ $team->subjectPreferences->count() }}/10)</span>
                </h2>

                @if($team->subjectPreferences->isEmpty())
                    <p class="text-gray-500 text-center py-8">
                        {{ __('app.no_subjects_selected_yet') }}
                    </p>
                @else
                    <div class="space-y-2" id="preference-list">
                        @foreach($team->subjectPreferences as $preference)
                            <div class="border rounded-lg p-3 flex justify-between items-center preference-item"
                                 data-subject-id="{{ $preference->subject_id }}"
                                 @if($preference->is_allocated) style="background-color: #f0fdf4;" @endif>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-bold rounded-full
                                            @if($preference->is_allocated) bg-green-500 text-white @else bg-gray-200 text-gray-700 @endif mr-3">
                                            {{ $preference->preference_order }}
                                        </span>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $preference->subject->title }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ __('app.teacher') }}: {{ $preference->subject->teacher->name ?? __('app.not_assigned') }}
                                            </p>
                                            @if($preference->is_allocated)
                                                <span class="inline-block mt-1 px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded">
                                                    {{ __('app.allocated') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($canManage && !$preference->is_allocated)
                                    <div class="flex items-center space-x-2">
                                        <button class="move-up text-gray-400 hover:text-gray-600"
                                                @if($loop->first) disabled @endif>
                                            <i class="fas fa-chevron-up"></i>
                                        </button>
                                        <button class="move-down text-gray-400 hover:text-gray-600"
                                                @if($loop->last) disabled @endif>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                        <form action="{{ route('teams.remove-subject-preference', [$team, $preference->subject]) }}"
                                              method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($canManage && $team->subjectPreferences->where('is_allocated', false)->count() > 1)
                        <form id="update-order-form" action="{{ route('teams.update-preference-order', $team) }}" method="POST" class="mt-4">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="subject_ids" id="subject-ids-input">
                            <button type="submit" class="w-full bg-blue-600 text-white rounded-lg px-4 py-2 hover:bg-blue-700 transition-colors">
                                {{ __('app.save_preference_order') }}
                            </button>
                        </form>
                    @endif
                @endif
            </div>

            <!-- Available Subjects -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('app.available_subjects') }}</h2>

                @if($canManage && $team->subjectPreferences->count() < 10)
                    <!-- Search Filter -->
                    <div class="mb-4">
                        <input type="text" id="subject-search"
                               placeholder="{{ __('app.search_subjects') }}..."
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="space-y-2 max-h-96 overflow-y-auto" id="available-subjects">
                        @foreach($availableSubjects as $subject)
                            <div class="border rounded-lg p-3 hover:bg-gray-50 subject-item">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 subject-title">{{ $subject->title }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ __('app.teacher') }}: {{ $subject->teacher->name ?? __('app.not_assigned') }}
                                        </p>
                                        @if($subject->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($subject->description, 100) }}</p>
                                        @endif
                                    </div>

                                    <form action="{{ route('teams.add-subject-preference', $team) }}" method="POST" class="ml-2">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                            <i class="fas fa-plus mr-1"></i> {{ __('app.add') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($availableSubjects->isEmpty())
                        <p class="text-gray-500 text-center py-8">
                            {{ __('app.no_available_subjects') }}
                        </p>
                    @endif
                @else
                    @if($team->subjectPreferences->count() >= 10)
                        <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                            <p class="text-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ __('app.maximum_subjects_reached') }}
                            </p>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-semibold text-blue-900 mb-2">{{ __('app.instructions') }}</h3>
            <ul class="text-sm text-blue-700 space-y-1">
                <li><i class="fas fa-check mr-1"></i> {{ __('app.select_up_to_10_subjects') }}</li>
                <li><i class="fas fa-check mr-1"></i> {{ __('app.order_by_preference') }}</li>
                <li><i class="fas fa-check mr-1"></i> {{ __('app.subject_allocation_based_on_preference') }}</li>
                <li><i class="fas fa-check mr-1"></i> {{ __('app.cannot_change_after_allocation') }}</li>
            </ul>
        </div>
    </div>
</div>

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
    const moveUpButtons = document.querySelectorAll('.move-up');
    const moveDownButtons = document.querySelectorAll('.move-down');
    const updateOrderForm = document.getElementById('update-order-form');
    const subjectIdsInput = document.getElementById('subject-ids-input');

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
            const orderBadge = item.querySelector('.rounded-full');
            if (orderBadge) {
                orderBadge.textContent = index + 1;
            }
        });
    }

    function updateButtons() {
        const items = document.querySelectorAll('.preference-item');
        items.forEach((item, index) => {
            const upBtn = item.querySelector('.move-up');
            const downBtn = item.querySelector('.move-down');

            if (upBtn) upBtn.disabled = index === 0;
            if (downBtn) downBtn.disabled = index === items.length - 1;
        });
    }

    moveUpButtons.forEach(button => {
        button.addEventListener('click', () => swapPreferences(button, 'up'));
    });

    moveDownButtons.forEach(button => {
        button.addEventListener('click', () => swapPreferences(button, 'down'));
    });

    // Update order form submission
    if (updateOrderForm) {
        updateOrderForm.addEventListener('submit', function(e) {
            const items = document.querySelectorAll('.preference-item');
            const subjectIds = Array.from(items).map(item => item.dataset.subjectId);
            subjectIdsInput.value = JSON.stringify(subjectIds);
        });
    }
});
</script>
@endpush
@endsection