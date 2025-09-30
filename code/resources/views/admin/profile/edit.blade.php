@extends('layouts.admin-modern')

@section('title', 'Profile Settings')
@section('page-title', 'Profile')

@section('content')
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Profile Settings</h1>
            <p class="text-gray-600 mt-1">Manage your account information and preferences</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
        <div class="lg:col-span-2">
            <!-- Basic Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 lg:mb-8">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Basic Information
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                                <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 mt-4 lg:mt-6">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Professional Title</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                                       id="title" name="title" value="{{ old('title', $user->title) }}"
                                       placeholder="e.g., Professor, Assistant Professor">
                                @error('title')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('department') border-red-500 @enderror"
                                       id="department" name="department" value="{{ old('department', $user->department) }}">
                                @error('department')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 mt-4 lg:mt-6">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('phone') border-red-500 @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="office_location" class="block text-sm font-medium text-gray-700 mb-2">Office Location</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('office_location') border-red-500 @enderror"
                                       id="office_location" name="office_location" value="{{ old('office_location', $user->office_location) }}">
                                @error('office_location')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4 lg:mt-6">
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Biography</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('bio') border-red-500 @enderror"
                                      id="bio" name="bio" rows="5">{{ old('bio', $user->bio) }}</textarea>
                            <p class="text-gray-500 text-sm mt-1">Brief professional biography for your public profile</p>
                            @error('bio')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4 lg:mt-6">
                            <label for="specializations" class="block text-sm font-medium text-gray-700 mb-2">Specializations</label>
                            <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('specializations') border-red-500 @enderror"
                                      id="specializations" name="specializations" rows="3">{{ old('specializations', $user->specializations) }}</textarea>
                            <p class="text-gray-500 text-sm mt-1">Your areas of expertise (one per line)</p>
                            @error('specializations')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-check mr-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 lg:mb-8">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-key mr-2 text-blue-600"></i>
                        Change Password
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <form method="POST" action="{{ route('admin.profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4 lg:mb-6">
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password *</label>
                            <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('current_password') border-red-500 @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password *</label>
                                <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('password') border-red-500 @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password *</label>
                                <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors font-medium">
                                <i class="fas fa-key mr-2"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Social Media Links -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg lg:text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-link mr-2 text-blue-600"></i>
                        Social Media & Links
                    </h2>
                </div>
                <div class="p-4 lg:p-6">
                    <form method="POST" action="{{ route('admin.profile.social') }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6">
                            <div>
                                <label for="website" class="block text-sm font-medium text-gray-700 mb-2">Personal Website</label>
                                <input type="url" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('website') border-red-500 @enderror"
                                       id="website" name="website" value="{{ old('website', $user->website) }}">
                                @error('website')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">LinkedIn Profile</label>
                                <input type="url" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('linkedin_url') border-red-500 @enderror"
                                       id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $user->linkedin_url) }}">
                                @error('linkedin_url')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 lg:gap-6 mt-4 lg:mt-6">
                            <div>
                                <label for="orcid_id" class="block text-sm font-medium text-gray-700 mb-2">ORCID iD</label>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('orcid_id') border-red-500 @enderror"
                                       id="orcid_id" name="orcid_id" value="{{ old('orcid_id', $user->orcid_id) }}"
                                       placeholder="0000-0000-0000-0000">
                                @error('orcid_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="google_scholar_url" class="block text-sm font-medium text-gray-700 mb-2">Google Scholar Profile</label>
                                <input type="url" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('google_scholar_url') border-red-500 @enderror"
                                       id="google_scholar_url" name="google_scholar_url" value="{{ old('google_scholar_url', $user->google_scholar_url) }}">
                                @error('google_scholar_url')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                <i class="fas fa-check mr-2"></i>Update Links
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <!-- Profile Picture -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 lg:mb-8">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-camera mr-2 text-blue-600"></i>
                        Profile Picture
                    </h2>
                </div>
                <div class="p-4 lg:p-6 text-center">
                    @if($user->profile_picture)
                        <img src="{{ Storage::url($user->profile_picture) }}"
                             alt="Profile Picture"
                             class="w-32 h-32 lg:w-40 lg:h-40 rounded-full mb-4 mx-auto object-cover">
                    @else
                        <div class="w-32 h-32 lg:w-40 lg:h-40 bg-gray-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <i class="fas fa-user text-gray-400 text-4xl"></i>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.profile.avatar') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('avatar') border-red-500 @enderror"
                                   id="avatar" name="avatar" accept="image/*">
                            <p class="text-gray-500 text-sm mt-1">Recommended: 400x400px</p>
                            @error('avatar')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        @if($user->profile_picture)
                            <div class="mb-4">
                                <div class="flex items-center justify-center">
                                    <input class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" type="checkbox" name="remove_avatar" id="remove_avatar">
                                    <label class="ml-2 text-sm text-gray-600" for="remove_avatar">
                                        Remove current picture
                                    </label>
                                </div>
                            </div>
                        @endif

                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="fas fa-upload mr-2"></i>Update Picture
                        </button>
                    </form>
                </div>
            </div>

            <!-- Account Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 lg:mb-8">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                        Account Information
                    </h2>
                </div>
                <div class="p-4 lg:p-6 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">User ID:</span>
                        <span class="font-medium text-gray-900">#{{ $user->id }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Role:</span>
                        <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">{{ ucfirst($user->role ?? 'Admin') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Member Since:</span>
                        <span class="font-medium text-gray-900">{{ $user->created_at->format('F j, Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Last Updated:</span>
                        <span class="font-medium text-gray-900">{{ $user->updated_at->format('M j, Y g:i A') }}</span>
                    </div>
                    @if($user->email_verified_at)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Email Verified:</span>
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium flex items-center">
                                <i class="fas fa-check-circle mr-1"></i>Verified
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 lg:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-bolt mr-2 text-blue-600"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-4 lg:p-6 space-y-3">
                    <a href="{{ route('home') }}" class="flex items-center w-full px-4 py-2 text-blue-600 border border-blue-300 rounded-lg hover:bg-blue-50 transition-colors" target="_blank">
                        <i class="fas fa-eye mr-2"></i>View Public Profile
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center w-full px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('password_confirmation');

    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            // Add visual feedback for password strength if needed
        });
    }

    if (confirmInput) {
        confirmInput.addEventListener('input', function() {
            if (passwordInput.value !== this.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Preview uploaded avatar
    const avatarInput = document.getElementById('avatar');
    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.querySelector('.rounded-full');
                    if (img && img.tagName === 'IMG') {
                        img.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>
@endpush