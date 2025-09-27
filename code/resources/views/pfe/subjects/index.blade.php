@extends('layouts.app')

@section('title', __('Subjects Management'))

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('Subjects Management') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Manage and oversee all PFE subjects') }}</p>
        </div>

        @can('create', App\Models\Subject::class)
        <a href="{{ route('pfe.subjects.create') }}"
           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-plus mr-2"></i>
            {{ __('Add Subject') }}
        </a>
        @endcan
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('pfe.subjects.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700">{{ __('Search') }}</label>
                    <input type="text" name="search" id="search"
                           value="{{ request('search') }}"
                           placeholder="{{ __('Search subjects...') }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                    <select name="status" id="status"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('All Statuses') }}</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                        <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>{{ __('Submitted') }}</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                    </select>
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700">{{ __('Department') }}</label>
                    <select name="department" id="department"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('All Departments') }}</option>
                        <option value="informatique" {{ request('department') == 'informatique' ? 'selected' : '' }}>{{ __('Computer Science') }}</option>
                        <option value="electronique" {{ request('department') == 'electronique' ? 'selected' : '' }}>{{ __('Electronics') }}</option>
                        <option value="mecanique" {{ request('department') == 'mecanique' ? 'selected' : '' }}>{{ __('Mechanical') }}</option>
                        <option value="civil" {{ request('department') == 'civil' ? 'selected' : '' }}>{{ __('Civil Engineering') }}</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-end space-x-2">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-search mr-2"></i>
                        {{ __('Filter') }}
                    </button>
                    <a href="{{ route('pfe.subjects.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">{{ __('Total Subjects') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">{{ __('Pending Approval') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-green-600">{{ $stats['approved'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">{{ __('Approved') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['published'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">{{ __('Published') }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">{{ __('Rejected') }}</div>
        </div>
    </div>

    <!-- Subjects List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">{{ __('Subjects List') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Subject') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Supervisor') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Department') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Status') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Teams') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Created') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subjects as $subject)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('pfe.subjects.show', $subject) }}" class="hover:text-indigo-600">
                                        {{ $subject->title }}
                                    </a>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ Str::limit($subject->description, 80) }}
                                </div>
                                @if($subject->keywords)
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach(array_slice($subject->keywords, 0, 3) as $keyword)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $keyword }}
                                    </span>
                                    @endforeach
                                    @if(count($subject->keywords) > 3)
                                    <span class="text-xs text-gray-500">+{{ count($subject->keywords) - 3 }} more</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img class="h-8 w-8 rounded-full"
                                     src="{{ $subject->supervisor->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($subject->supervisor->name) }}"
                                     alt="{{ $subject->supervisor->name }}">
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $subject->supervisor->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $subject->supervisor->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ __(ucfirst($subject->department ?? 'N/A')) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $subject->status === 'published' ? 'bg-blue-100 text-blue-800' :
                                   ($subject->status === 'approved' ? 'bg-green-100 text-green-800' :
                                   ($subject->status === 'rejected' ? 'bg-red-100 text-red-800' :
                                   ($subject->status === 'submitted' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ __(ucfirst($subject->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <span class="text-lg font-medium">{{ $subject->assigned_teams_count ?? 0 }}</span>
                                <span class="text-gray-500 ml-1">/ {{ $subject->max_teams }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $subject->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('pfe.subjects.show', $subject) }}"
                                   class="text-indigo-600 hover:text-indigo-900">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @can('update', $subject)
                                <a href="{{ route('pfe.subjects.edit', $subject) }}"
                                   class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @hasrole('chef_master')
                                @if($subject->status === 'submitted')
                                <a href="{{ route('pfe.subjects.validation', $subject) }}"
                                   class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                                @endif

                                @if($subject->status === 'approved' && !$subject->isPublished())
                                <form method="POST" action="{{ route('pfe.subjects.publish', $subject) }}" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="text-blue-600 hover:text-blue-900"
                                            onclick="return confirm('{{ __('Are you sure you want to publish this subject?') }}')">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                                @endif
                                @endhasrole

                                @can('delete', $subject)
                                <form method="POST" action="{{ route('pfe.subjects.destroy', $subject) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('{{ __('Are you sure you want to delete this subject?') }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-center">
                                <i class="fas fa-book text-gray-400 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('No subjects found') }}</h3>
                                <p class="text-gray-500 mb-4">
                                    @if(request()->hasAny(['search', 'status', 'department']))
                                        {{ __('No subjects match your current filters.') }}
                                    @else
                                        {{ __('Get started by creating your first subject.') }}
                                    @endif
                                </p>
                                @can('create', App\Models\Subject::class)
                                <a href="{{ route('pfe.subjects.create') }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                    <i class="fas fa-plus mr-2"></i>
                                    {{ __('Create Subject') }}
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $subjects->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
    const filters = ['status', 'department'];
    filters.forEach(filterId => {
        const element = document.getElementById(filterId);
        if (element) {
            element.addEventListener('change', function() {
                this.form.submit();
            });
        }
    });
});
</script>
@endpush