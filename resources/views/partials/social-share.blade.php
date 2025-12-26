<!-- Social Sharing Component -->
@php
    $currentUrl = urlencode(request()->fullUrl());
    $title = urlencode($title ?? 'Check this out!');
    $description = urlencode($description ?? '');
@endphp

<div class="social-share">
    @if(!isset($hideTitle) || !$hideTitle)
        <h6 class="mb-3">
            <i class="bi bi-share me-2"></i>Share this {{ $type ?? 'content' }}
        </h6>
    @endif

    <div class="d-flex flex-wrap gap-2">
        <!-- Facebook -->
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $currentUrl }}"
           target="_blank"
           rel="noopener"
           class="btn btn-outline-primary btn-sm"
           title="Share on Facebook">
            <i class="bi bi-facebook"></i>
            <span class="d-none d-sm-inline ms-1">Facebook</span>
        </a>

        <!-- Twitter -->
        <a href="https://twitter.com/intent/tweet?url={{ $currentUrl }}&text={{ $title }}"
           target="_blank"
           rel="noopener"
           class="btn btn-outline-info btn-sm"
           title="Share on Twitter">
            <i class="bi bi-twitter"></i>
            <span class="d-none d-sm-inline ms-1">Twitter</span>
        </a>

        <!-- LinkedIn -->
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $currentUrl }}"
           target="_blank"
           rel="noopener"
           class="btn btn-outline-primary btn-sm"
           title="Share on LinkedIn">
            <i class="bi bi-linkedin"></i>
            <span class="d-none d-sm-inline ms-1">LinkedIn</span>
        </a>

        <!-- Email -->
        <a href="mailto:?subject={{ $title }}&body=I thought you might be interested in this: {{ $currentUrl }}"
           class="btn btn-outline-secondary btn-sm"
           title="Share via Email">
            <i class="bi bi-envelope"></i>
            <span class="d-none d-sm-inline ms-1">Email</span>
        </a>

        <!-- Copy Link -->
        <button type="button"
                class="btn btn-outline-dark btn-sm"
                onclick="copyToClipboard('{{ request()->fullUrl() }}')"
                title="Copy Link">
            <i class="bi bi-clipboard"></i>
            <span class="d-none d-sm-inline ms-1">Copy Link</span>
        </button>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success feedback
        const btn = event.target.closest('button');
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i><span class="d-none d-sm-inline ms-1">Copied!</span>';
        btn.classList.remove('btn-outline-dark');
        btn.classList.add('btn-success');

        setTimeout(function() {
            btn.innerHTML = originalHtml;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-dark');
        }, 2000);
    }).catch(function() {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);

        alert('Link copied to clipboard!');
    });
}
</script>