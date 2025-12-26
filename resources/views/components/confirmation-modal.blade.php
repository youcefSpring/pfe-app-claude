{{-- Reusable Confirmation Modal Component --}}
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">
                    <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                    {{ __('app.confirm_action') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('app.close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                            <i class="bi bi-question-circle text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-2" id="confirmationTitle">{{ __('app.are_you_sure') }}</h6>
                        <p class="mb-0 text-muted" id="confirmationMessage">
                            {{ __('app.confirm_action_description') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>{{ __('app.cancel') }}
                </button>
                <button type="button" class="btn btn-danger" id="confirmActionBtn">
                    <i class="bi bi-check-circle me-1"></i>{{ __('app.confirm') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Confirmation Modal JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    const confirmBtn = document.getElementById('confirmActionBtn');
    let pendingAction = null;

    // Global function to show confirmation modal
    window.showConfirmation = function(options) {
        const {
            title = '{{ __('app.confirm_action') }}',
            message = '{{ __('app.are_you_sure') }}',
            confirmText = '{{ __('app.confirm') }}',
            confirmClass = 'btn-danger',
            onConfirm = null,
            form = null
        } = options;

        // Update modal content
        document.getElementById('confirmationTitle').innerHTML = title;
        document.getElementById('confirmationMessage').innerHTML = message;

        // Update confirm button
        confirmBtn.innerHTML = confirmText;
        confirmBtn.className = `btn ${confirmClass}`;

        // Store the action to perform
        pendingAction = { onConfirm, form };

        // Show modal
        confirmationModal.show();

        return false; // Prevent default action
    };

    // Handle confirm button click
    confirmBtn.addEventListener('click', function() {
        if (pendingAction) {
            if (pendingAction.form) {
                // Submit the form
                pendingAction.form.submit();
            } else if (pendingAction.onConfirm) {
                // Execute callback
                pendingAction.onConfirm();
            }
        }

        confirmationModal.hide();
        pendingAction = null;
    });

    // Clean up when modal is hidden
    document.getElementById('confirmationModal').addEventListener('hidden.bs.modal', function() {
        pendingAction = null;
    });

    // Helper function for form submissions
    window.confirmFormSubmission = function(form, message, title) {
        return showConfirmation({
            title: title || '{{ __('app.confirm_action') }}',
            message: message || '{{ __('app.are_you_sure') }}',
            form: form
        });
    };

    // Helper function for delete actions
    window.confirmDelete = function(itemName, onConfirm, form) {
        return showConfirmation({
            title: '{{ __('app.confirm_delete') }}',
            message: '{{ __('app.confirm_delete_message') }}'.replace(':item', itemName),
            confirmText: '{{ __('app.delete') }}',
            confirmClass: 'btn-danger',
            onConfirm: onConfirm,
            form: form
        });
    };

    // Helper function for leave actions
    window.confirmLeave = function(message, onConfirm, form) {
        return showConfirmation({
            title: '{{ __('app.confirm_leave') }}',
            message: message,
            confirmText: '{{ __('app.leave') }}',
            confirmClass: 'btn-warning',
            onConfirm: onConfirm,
            form: form
        });
    };

    // Simple alert function for informational messages
    window.showAlert = function(title, message, type = 'info') {
        const alertClass = {
            'info': 'btn-primary',
            'warning': 'btn-warning',
            'danger': 'btn-danger',
            'success': 'btn-success'
        }[type] || 'btn-primary';

        return showConfirmation({
            title: title,
            message: message,
            confirmText: '{{ __('app.ok') }}',
            confirmClass: alertClass,
            onConfirm: function() {
                // Just close the modal, no action needed
            }
        });
    };
});
</script>

<style>
.modal-content {
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    background-color: #f8f9fa;
}

.modal-dialog-centered {
    min-height: calc(100vh - 1rem);
}

@media (min-width: 576px) {
    .modal-dialog-centered {
        min-height: calc(100vh - 3.5rem);
    }
}

/* Loading state for confirm button */
.btn.loading {
    position: relative;
    color: transparent;
}

.btn.loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 1s ease infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>