@extends('layouts.app')

@section('title', __('Create Subject'))

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="text-sm text-gray-500 mb-4">
            <a href="{{ route('pfe.subjects.index') }}" class="hover:text-gray-700">{{ __('Subjects') }}</a>
            <span class="mx-2">/</span>
            <span class="text-gray-900">{{ __('Create Subject') }}</span>
        </nav>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('Create New Subject') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('Propose a new PFE subject for students to work on.') }}</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" action="{{ route('pfe.subjects.store') }}" class="p-6 space-y-6">
            @csrf

            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Basic Information') }}</h3>

                <!-- Title -->
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-gray-700">{{ __('Subject Title') }}</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-500 @enderror"
                           placeholder="{{ __('Enter a clear and descriptive title for your subject') }}"
                           maxlength="200" required>
                    @error('title')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                    <textarea name="description" id="description" rows="6"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-500 @enderror"
                              placeholder="{{ __('Provide a detailed description of the project, objectives, and expected outcomes (minimum 100 characters)') }}"
                              required>{{ old('description') }}</textarea>
                    <p class="mt-2 text-sm text-gray-500">{{ __('Minimum 100 characters required') }}</p>
                    @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div class="mb-4">
                    <label for="department" class="block text-sm font-medium text-gray-700">{{ __('Department') }}</label>
                    <select name="department" id="department"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('department') border-red-500 @enderror"
                            required>
                        <option value="">{{ __('Select Department') }}</option>
                        <option value="informatique" {{ old('department') == 'informatique' ? 'selected' : '' }}>{{ __('Computer Science') }}</option>
                        <option value="electronique" {{ old('department') == 'electronique' ? 'selected' : '' }}>{{ __('Electronics') }}</option>
                        <option value="mecanique" {{ old('department') == 'mecanique' ? 'selected' : '' }}>{{ __('Mechanical Engineering') }}</option>
                        <option value="civil" {{ old('department') == 'civil' ? 'selected' : '' }}>{{ __('Civil Engineering') }}</option>
                        <option value="electrique" {{ old('department') == 'electrique' ? 'selected' : '' }}>{{ __('Electrical Engineering') }}</option>
                    </select>
                    @error('department')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Technical Details -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Technical Details') }}</h3>

                <!-- Keywords -->
                <div class="mb-4">
                    <label for="keywords" class="block text-sm font-medium text-gray-700">{{ __('Keywords') }}</label>
                    <input type="text" name="keywords" id="keywords"
                           value="{{ old('keywords') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('keywords') border-red-500 @enderror"
                           placeholder="{{ __('Enter keywords separated by commas (e.g., web development, AI, machine learning)') }}">
                    <p class="mt-2 text-sm text-gray-500">{{ __('Enter 3-10 keywords separated by commas') }}</p>
                    @error('keywords')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Required Tools -->
                <div class="mb-4">
                    <label for="required_tools" class="block text-sm font-medium text-gray-700">{{ __('Required Tools & Technologies') }}</label>
                    <input type="text" name="required_tools" id="required_tools"
                           value="{{ old('required_tools') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="{{ __('e.g., React, Node.js, Python, TensorFlow, etc.') }}">
                    <p class="mt-2 text-sm text-gray-500">{{ __('List the main tools and technologies students will use') }}</p>
                    @error('required_tools')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Difficulty Level -->
                <div class="mb-4">
                    <label for="difficulty_level" class="block text-sm font-medium text-gray-700">{{ __('Difficulty Level') }}</label>
                    <select name="difficulty_level" id="difficulty_level"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="beginner" {{ old('difficulty_level') == 'beginner' ? 'selected' : '' }}>{{ __('Beginner') }}</option>
                        <option value="intermediate" {{ old('difficulty_level', 'intermediate') == 'intermediate' ? 'selected' : '' }}>{{ __('Intermediate') }}</option>
                        <option value="advanced" {{ old('difficulty_level') == 'advanced' ? 'selected' : '' }}>{{ __('Advanced') }}</option>
                        <option value="expert" {{ old('difficulty_level') == 'expert' ? 'selected' : '' }}>{{ __('Expert') }}</option>
                    </select>
                    @error('difficulty_level')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Team Configuration -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Team Configuration') }}</h3>

                <!-- Max Teams -->
                <div class="mb-4">
                    <label for="max_teams" class="block text-sm font-medium text-gray-700">{{ __('Maximum Number of Teams') }}</label>
                    <select name="max_teams" id="max_teams"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('max_teams') border-red-500 @enderror"
                            required>
                        <option value="1" {{ old('max_teams', '1') == '1' ? 'selected' : '' }}>{{ __('1 team only') }}</option>
                        <option value="2" {{ old('max_teams') == '2' ? 'selected' : '' }}>{{ __('2 teams maximum') }}</option>
                        <option value="3" {{ old('max_teams') == '3' ? 'selected' : '' }}>{{ __('3 teams maximum') }}</option>
                    </select>
                    <p class="mt-2 text-sm text-gray-500">{{ __('How many teams can work on this subject simultaneously?') }}</p>
                    @error('max_teams')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Recommended Team Size -->
                <div class="mb-4">
                    <label for="recommended_team_size" class="block text-sm font-medium text-gray-700">{{ __('Recommended Team Size') }}</label>
                    <select name="recommended_team_size" id="recommended_team_size"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="1" {{ old('recommended_team_size') == '1' ? 'selected' : '' }}>{{ __('1 student (Individual)') }}</option>
                        <option value="2" {{ old('recommended_team_size', '2') == '2' ? 'selected' : '' }}>{{ __('2 students') }}</option>
                        <option value="3" {{ old('recommended_team_size') == '3' ? 'selected' : '' }}>{{ __('3 students') }}</option>
                        <option value="4" {{ old('recommended_team_size') == '4' ? 'selected' : '' }}>{{ __('4 students') }}</option>
                    </select>
                    @error('recommended_team_size')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- External Collaboration -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('External Collaboration') }}</h3>

                <!-- External Supervisor -->
                <div class="mb-4">
                    <label for="external_supervisor" class="block text-sm font-medium text-gray-700">{{ __('External Supervisor (Optional)') }}</label>
                    <input type="text" name="external_supervisor" id="external_supervisor"
                           value="{{ old('external_supervisor') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="{{ __('Name and title of external supervisor (if applicable)') }}">
                    <p class="mt-2 text-sm text-gray-500">{{ __('If this project involves an external company or organization') }}</p>
                    @error('external_supervisor')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Company/Organization -->
                <div class="mb-4">
                    <label for="external_company" class="block text-sm font-medium text-gray-700">{{ __('Company/Organization (Optional)') }}</label>
                    <input type="text" name="external_company" id="external_company"
                           value="{{ old('external_company') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="{{ __('Company or organization name') }}">
                    @error('external_company')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Additional Information') }}</h3>

                <!-- Prerequisites -->
                <div class="mb-4">
                    <label for="prerequisites" class="block text-sm font-medium text-gray-700">{{ __('Prerequisites (Optional)') }}</label>
                    <textarea name="prerequisites" id="prerequisites" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="{{ __('Any specific knowledge, courses, or skills required') }}">{{ old('prerequisites') }}</textarea>
                    @error('prerequisites')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expected Deliverables -->
                <div class="mb-4">
                    <label for="expected_deliverables" class="block text-sm font-medium text-gray-700">{{ __('Expected Deliverables (Optional)') }}</label>
                    <textarea name="expected_deliverables" id="expected_deliverables" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="{{ __('List the main deliverables expected from this project') }}">{{ old('expected_deliverables') }}</textarea>
                    @error('expected_deliverables')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('pfe.subjects.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" name="action" value="draft"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Save as Draft') }}
                </button>
                <button type="submit" name="action" value="submit"
                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Submit for Review') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Character count for description
document.getElementById('description').addEventListener('input', function() {
    const maxLength = 2000;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;

    // You can add a character counter here if needed
});

// Keywords validation
document.getElementById('keywords').addEventListener('blur', function() {
    const keywords = this.value.split(',').map(k => k.trim()).filter(k => k.length > 0);
    if (keywords.length < 3) {
        // Show validation message
    }
});
</script>
@endpush