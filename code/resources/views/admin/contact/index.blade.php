@extends('layouts.admin-modern')

@section('title', 'Contact Messages')
@section('page-title', 'Messages')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Contact Messages</h1>
            <p class="text-gray-600 mt-1">Manage incoming contact form submissions</p>
        </div>
        <div class="flex items-center space-x-3">
            <button class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium" onclick="toggleBulkActions()">
                <i class="fas fa-list-check mr-2"></i>
                Bulk Actions
            </button>
            <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium" onclick="markAllAsRead()">
                <i class="fas fa-eye mr-2"></i>
                Mark All Read
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 text-center">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-envelope text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-2xl lg:text-3xl font-bold text-blue-600 mb-1">{{ $statusCounts['unread'] ?? 0 }}</h3>
            <p class="text-gray-600 text-sm">Unread</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 text-center">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-check text-green-600 text-xl"></i>
            </div>
            <h3 class="text-2xl lg:text-3xl font-bold text-green-600 mb-1">{{ $statusCounts['read'] ?? 0 }}</h3>
            <p class="text-gray-600 text-sm">Read</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 text-center">
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-reply text-purple-600 text-xl"></i>
            </div>
            <h3 class="text-2xl lg:text-3xl font-bold text-purple-600 mb-1">{{ $statusCounts['replied'] ?? 0 }}</h3>
            <p class="text-gray-600 text-sm">Replied</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-2xl lg:text-3xl font-bold text-red-600 mb-1">{{ $statusCounts['spam'] ?? 0 }}</h3>
            <p class="text-gray-600 text-sm">Spam</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 mb-6 lg:mb-8">
        <form method="GET" action="{{ route('admin.contact.index') }}" class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                       id="search"
                       name="search"
                       placeholder="Search by name, email, or subject..."
                       value="{{ request('search') }}">
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Unread</option>
                    <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                    <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Replied</option>
                    <option value="spam" {{ request('status') === 'spam' ? 'selected' : '' }}>Spam</option>
                </select>
            </div>
            <div>
                <label for="date_range" class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" id="date_range" name="date_range">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>This Month</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
                <a href="{{ route('admin.contact.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div id="bulkActionsBar" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <span class="text-sm font-medium text-yellow-800" id="selectedCount">0 messages selected</span>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="bulkAction('read')" class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm hover:bg-green-200 transition-colors">
                    Mark as Read
                </button>
                <button onclick="bulkAction('replied')" class="px-3 py-1 bg-purple-100 text-purple-700 rounded text-sm hover:bg-purple-200 transition-colors">
                    Mark as Replied
                </button>
                <button onclick="bulkAction('spam')" class="px-3 py-1 bg-orange-100 text-orange-700 rounded text-sm hover:bg-orange-200 transition-colors">
                    Mark as Spam
                </button>
                <button onclick="bulkAction('delete')" class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm hover:bg-red-200 transition-colors">
                    Delete
                </button>
                <button onclick="clearSelection()" class="px-3 py-1 bg-gray-100 text-gray-700 rounded text-sm hover:bg-gray-200 transition-colors">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <div class="p-4 lg:p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-envelope mr-2 text-blue-600"></i>
                    Messages ({{ $messages->total() }})
                </h2>
                <div class="flex items-center">
                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                    <label for="selectAll" class="text-sm text-gray-600">Select All</label>
                </div>
            </div>
        </div>

        @if($messages->count() > 0)
            <!-- Desktop Table -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-sm text-gray-600 border-b border-gray-100">
                            <th class="pb-3 px-6 font-medium w-12">
                                <input type="checkbox" id="selectAllHeader" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="pb-3 px-3 font-medium">Contact Details</th>
                            <th class="pb-3 px-3 font-medium">Subject</th>
                            <th class="pb-3 px-3 font-medium">Status</th>
                            <th class="pb-3 px-3 font-medium">Date</th>
                            <th class="pb-3 px-6 font-medium w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($messages as $message)
                            <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors {{ $message->status === 'unread' ? 'bg-blue-50' : '' }}">
                                <td class="py-4 px-6">
                                    <input type="checkbox" class="message-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" value="{{ $message->id }}">
                                </td>
                                <td class="py-4 px-3">
                                    <div>
                                        <h3 class="font-medium text-gray-900 mb-1 flex items-center">
                                            <a href="{{ route('admin.contact.show', $message) }}" class="hover:text-blue-600 transition-colors">
                                                {{ $message->name }}
                                            </a>
                                            @if($message->status === 'unread')
                                                <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">New</span>
                                            @endif
                                        </h3>
                                        <p class="text-gray-500 text-xs mb-1 flex items-center">
                                            <i class="fas fa-envelope mr-1"></i>{{ $message->email }}
                                        </p>
                                        <p class="text-gray-500 text-xs">{{ Str::limit($message->message, 80) }}</p>
                                    </div>
                                </td>
                                <td class="py-4 px-3">
                                    <span class="font-medium text-gray-900">{{ $message->subject }}</span>
                                </td>
                                <td class="py-4 px-3">
                                    @if($message->status === 'unread')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Unread</span>
                                    @elseif($message->status === 'read')
                                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Read</span>
                                    @elseif($message->status === 'replied')
                                        <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">Replied</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Spam</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-gray-500 text-xs">
                                    {{ $message->created_at->format('M j, Y') }}<br>
                                    {{ $message->created_at->format('g:i A') }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.contact.show', $message) }}" class="text-blue-600 hover:text-blue-700 transition-colors" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($message->status === 'unread')
                                            <button onclick="updateStatus({{ $message->id }}, 'read')" class="text-green-600 hover:text-green-700 transition-colors" title="Mark as Read">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        @if($message->status !== 'replied')
                                            <button onclick="updateStatus({{ $message->id }}, 'replied')" class="text-purple-600 hover:text-purple-700 transition-colors" title="Mark as Replied">
                                                <i class="fas fa-reply"></i>
                                            </button>
                                        @endif
                                        <form method="POST" action="{{ route('admin.contact.destroy', $message) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 transition-colors" title="Delete" onclick="return confirm('Are you sure you want to delete this message?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="lg:hidden divide-y divide-gray-100">
                @foreach($messages as $message)
                    <div class="p-4 lg:p-6 {{ $message->status === 'unread' ? 'bg-blue-50' : '' }}">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-start space-x-3 flex-1">
                                <input type="checkbox" class="message-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-1" value="{{ $message->id }}">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <h3 class="font-medium text-gray-900">
                                            <a href="{{ route('admin.contact.show', $message) }}" class="hover:text-blue-600 transition-colors">
                                                {{ $message->name }}
                                            </a>
                                        </h3>
                                        @if($message->status === 'unread')
                                            <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">New</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1">{{ $message->email }}</p>
                                    <p class="text-sm font-medium text-gray-900 mb-2">{{ $message->subject }}</p>
                                    <p class="text-sm text-gray-500 mb-3">{{ Str::limit($message->message, 100) }}</p>
                                </div>
                            </div>
                            <div class="ml-4">
                                @if($message->status === 'unread')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium">Unread</span>
                                @elseif($message->status === 'read')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">Read</span>
                                @elseif($message->status === 'replied')
                                    <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">Replied</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">Spam</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
                            <span class="flex items-center">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $message->created_at->format('M j, Y g:i A') }}
                            </span>
                        </div>

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('admin.contact.show', $message) }}" class="text-blue-600 hover:text-blue-700 transition-colors text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>
                            @if($message->status === 'unread')
                                <button onclick="updateStatus({{ $message->id }}, 'read')" class="text-green-600 hover:text-green-700 transition-colors text-sm font-medium">
                                    <i class="fas fa-check mr-1"></i>Mark Read
                                </button>
                            @endif
                            @if($message->status !== 'replied')
                                <button onclick="updateStatus({{ $message->id }}, 'replied')" class="text-purple-600 hover:text-purple-700 transition-colors text-sm font-medium">
                                    <i class="fas fa-reply mr-1"></i>Mark Replied
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($messages->hasPages())
                <div class="p-4 lg:p-6 border-t border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-gray-600 mb-4 sm:mb-0">
                            Showing {{ $messages->firstItem() }} to {{ $messages->lastItem() }} of {{ $messages->total() }} results
                        </p>
                        <div class="flex items-center space-x-2">
                            {{ $messages->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="p-8 lg:p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-gray-400 text-2xl"></i>
                </div>
                @if(request()->hasAny(['search', 'status', 'date_range']))
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No messages found</h3>
                    <p class="text-gray-600 mb-4">No messages match your current filters.</p>
                    <a href="{{ route('admin.contact.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Clear Filters
                    </a>
                @else
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No messages yet</h3>
                    <p class="text-gray-600">Contact form submissions will appear here.</p>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select All functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const selectAllHeader = document.getElementById('selectAllHeader');
        const messageCheckboxes = document.querySelectorAll('.message-checkbox');

        function updateSelectAll() {
            const checkedCount = document.querySelectorAll('.message-checkbox:checked').length;
            const totalCount = messageCheckboxes.length;

            if (selectAllCheckbox) selectAllCheckbox.checked = checkedCount === totalCount;
            if (selectAllHeader) selectAllHeader.checked = checkedCount === totalCount;

            // Update bulk actions bar
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectedCountSpan = document.getElementById('selectedCount');

            if (checkedCount > 0) {
                bulkActionsBar.classList.remove('hidden');
                selectedCountSpan.textContent = `${checkedCount} message${checkedCount === 1 ? '' : 's'} selected`;
            } else {
                bulkActionsBar.classList.add('hidden');
            }
        }

        [selectAllCheckbox, selectAllHeader].forEach(checkbox => {
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    messageCheckboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateSelectAll();
                });
            }
        });

        messageCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAll);
        });

        // Auto-submit filters
        const statusSelect = document.getElementById('status');
        const dateRangeSelect = document.getElementById('date_range');

        [statusSelect, dateRangeSelect].forEach(select => {
            if (select) {
                select.addEventListener('change', function() {
                    this.form.submit();
                });
            }
        });
    });

    function toggleBulkActions() {
        const checkedCount = document.querySelectorAll('.message-checkbox:checked').length;
        if (checkedCount === 0) {
            alert('Please select at least one message first.');
            return;
        }

        const bulkActionsBar = document.getElementById('bulkActionsBar');
        bulkActionsBar.classList.toggle('hidden');
    }

    function clearSelection() {
        document.querySelectorAll('.message-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('bulkActionsBar').classList.add('hidden');
        if (document.getElementById('selectAll')) document.getElementById('selectAll').checked = false;
        if (document.getElementById('selectAllHeader')) document.getElementById('selectAllHeader').checked = false;
    }

    function updateStatus(messageId, status) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/contact/${messageId}/status`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);

        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);

        document.body.appendChild(form);
        form.submit();
    }

    function bulkAction(action) {
        const selectedIds = Array.from(document.querySelectorAll('.message-checkbox:checked')).map(cb => cb.value);

        if (selectedIds.length === 0) {
            alert('Please select at least one message.');
            return;
        }

        const confirmMessage = action === 'delete'
            ? 'Are you sure you want to delete the selected messages? This action cannot be undone.'
            : `Are you sure you want to mark ${selectedIds.length} message(s) as ${action}?`;

        if (!confirm(confirmMessage)) {
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';

        if (action === 'delete') {
            form.action = '{{ route("admin.contact.bulk-delete") }}';
        } else {
            form.action = '{{ route("admin.contact.bulk-status") }}';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
        }

        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'message_ids[]';
            input.value = id;
            form.appendChild(input);
        });

        if (action !== 'delete') {
            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = action;
            form.appendChild(statusInput);
        }

        document.body.appendChild(form);
        form.submit();
    }

    function markAllAsRead() {
        if (confirm('Are you sure you want to mark all messages as read?')) {
            window.location.href = '{{ route("admin.contact.index") }}?mark_all_read=1';
        }
    }
</script>
@endpush