<!-- Footer -->
<footer class="text-white py-4 mt-5" style="background-color: #1f2937;">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h5>{{ config('app.name', 'Teacher Portfolio') }}</h5>
                <p class="text-light mb-0">Sharing knowledge through teaching, research, and development.</p>

                @if(isset($teacher) && $teacher->bio)
                    <p class="text-light small mt-2">{{ Str::limit($teacher->bio, 120) }}</p>
                @endif
            </div>
            <div class="col-md-6 text-md-end">
                <div class="mb-3">
                    <h6 class="text-light">Quick Links</h6>
                    <div class="d-flex flex-column flex-md-row justify-content-md-end gap-2">
                        <a href="{{ route('about') }}" class="text-light text-decoration-none">
                            <i class="bi bi-person me-1"></i>About
                        </a>
                        <a href="{{ route('courses.index') }}" class="text-light text-decoration-none">
                            <i class="bi bi-book me-1"></i>Courses
                        </a>
                        <a href="{{ route('publications.index') }}" class="text-light text-decoration-none">
                            <i class="bi bi-journal-text me-1"></i>Publications
                        </a>
                        <a href="{{ route('contact.show') }}" class="text-light text-decoration-none">
                            <i class="bi bi-envelope me-1"></i>Contact
                        </a>
                    </div>
                </div>

                <div class="mb-2">
                    <a href="{{ route('contact.show') }}" class="text-light text-decoration-none me-3">
                        <i class="bi bi-envelope"></i> Contact
                    </a>
                    <a href="{{ route('download-cv') }}" class="text-light text-decoration-none">
                        <i class="bi bi-download"></i> Download CV
                    </a>
                </div>

                @if(isset($teacher))
                    @if($teacher->linkedin_url || $teacher->website || $teacher->google_scholar_url)
                        <div class="mb-2">
                            @if($teacher->website)
                                <a href="{{ $teacher->website }}" target="_blank" rel="noopener" class="text-light me-3">
                                    <i class="bi bi-globe"></i>
                                </a>
                            @endif
                            @if($teacher->linkedin_url)
                                <a href="{{ $teacher->linkedin_url }}" target="_blank" rel="noopener" class="text-light me-3">
                                    <i class="bi bi-linkedin"></i>
                                </a>
                            @endif
                            @if($teacher->google_scholar_url)
                                <a href="{{ $teacher->google_scholar_url }}" target="_blank" rel="noopener" class="text-light me-3">
                                    <i class="bi bi-mortarboard"></i>
                                </a>
                            @endif
                            @if($teacher->orcid_id)
                                <a href="https://orcid.org/{{ $teacher->orcid_id }}" target="_blank" rel="noopener" class="text-light">
                                    <i class="bi bi-person-badge"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                @endif

                <small class="text-light">
                    © {{ date('Y') }} {{ config('app.name', 'Teacher Portfolio') }}. All rights reserved.
                </small>
            </div>
        </div>
    </div>
</footer>