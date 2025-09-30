@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope me-2"></i>Contact Messages
                        @if($contacts->where('read_at', null)->count() > 0)
                            <span class="badge bg-danger">{{ $contacts->where('read_at', null)->count() }} new</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($contacts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contacts as $contact)
                                        <tr class="{{ $contact->isRead() ? '' : 'table-warning' }}">
                                            <td>
                                                @if($contact->isRead())
                                                    <span class="badge bg-success">Read</span>
                                                @else
                                                    <span class="badge bg-danger">Unread</span>
                                                @endif
                                            </td>
                                            <td>{{ $contact->name }}</td>
                                            <td>{{ $contact->email }}</td>
                                            <td>{{ Str::limit($contact->subject, 50) }}</td>
                                            <td>{{ $contact->created_at->format('M j, Y g:i A') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.contacts.show', $contact) }}"
                                                       class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin.contacts.destroy', $contact) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this message?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger">
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

                        {{ $contacts->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-envelope-open text-muted mb-3" style="font-size: 3rem;"></i>
                            <h5 class="text-muted">No contact messages yet</h5>
                            <p class="text-muted">Contact messages will appear here when submitted.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection