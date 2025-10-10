{{-- Delete Confirmation Modal Component --}}
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger" id="deleteConfirmationModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ __('app.confirm_delete') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('app.close') }}"></button>
            </div>
            <div class="modal-body py-4">
                <div class="text-center">
                    <div class="mb-3">
                        <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                            <i class="bi bi-trash text-danger fs-2"></i>
                        </div>
                    </div>
                    <h5 class="mb-2">{{ __('app.are_you_sure') }}</h5>
                    <p class="text-muted mb-0" id="deleteMessage">
                        {{ __('app.confirm_delete_description') }}
                    </p>
                    <div class="mt-3 p-3 bg-warning bg-opacity-10 rounded">
                        <small class="text-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            {{ __('app.action_cannot_be_undone') }}
                        </small>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>{{ __('app.cancel') }}
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>{{ __('app.delete') }}
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    let pendingDeleteAction = null;

    // Global function to show delete confirmation modal
    window.showDeleteConfirmation = function(options) {
        const {
            message = '{{ __('app.confirm_delete_description') }}',
            itemName = '',
            onConfirm = null,
            form = null
        } = options;

        // Update modal content
        let displayMessage = message;
        if (itemName) {
            displayMessage = '{{ __('app.confirm_delete_item_message') }}'.replace(':item', itemName);
        }
        document.getElementById('deleteMessage').textContent = displayMessage;

        // Store the action to perform
        pendingDeleteAction = { onConfirm, form };

        // Show modal
        deleteModal.show();

        return false; // Prevent default action
    };

    // Handle confirm delete button click
    confirmDeleteBtn.addEventListener('click', function() {
        if (pendingDeleteAction) {
            // Add loading state
            this.classList.add('loading');
            this.disabled = true;

            if (pendingDeleteAction.form) {
                // Submit the form
                pendingDeleteAction.form.submit();
            } else if (pendingDeleteAction.onConfirm) {
                // Execute callback
                pendingDeleteAction.onConfirm();
            }
        }

        deleteModal.hide();
        pendingDeleteAction = null;
    });

    // Clean up when modal is hidden
    document.getElementById('deleteConfirmationModal').addEventListener('hidden.bs.modal', function() {
        pendingDeleteAction = null;
        confirmDeleteBtn.classList.remove('loading');
        confirmDeleteBtn.disabled = false;
    });

    // Helper function for delete forms
    window.confirmDelete = function(form, itemName, message) {
        return showDeleteConfirmation({
            message: message,
            itemName: itemName,
            form: form
        });
    };
});
</script>

<style>
#deleteConfirmationModal .modal-content {
    border: none;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

#deleteConfirmationModal .modal-body {
    padding: 2rem;
}

#deleteConfirmationModal .btn.loading {
    position: relative;
    color: transparent;
}

#deleteConfirmationModal .btn.loading::after {
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