{{-- Confirmation Modal Examples and Documentation --}}
{{-- This file demonstrates how to use the confirmation modal components --}}

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Confirmation Modal Examples</h4>
                    <p class="text-muted mb-0">Professional confirmation dialogs to replace JavaScript alerts</p>
                </div>
                <div class="card-body">

                    {{-- Basic Confirmation Examples --}}
                    <div class="mb-5">
                        <h5 class="mb-3">1. Basic Confirmation Modal</h5>
                        <p class="text-muted">Replace simple JavaScript <code>confirm()</code> dialogs:</p>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <button type="button" class="btn btn-warning w-100"
                                        onclick="showConfirmation({
                                            title: 'Save Changes',
                                            message: 'Do you want to save your changes?',
                                            confirmText: 'Save',
                                            confirmClass: 'btn-success',
                                            onConfirm: function() { alert('Changes saved!'); }
                                        })">
                                    <i class="bi bi-save me-1"></i>Save Document
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-info w-100"
                                        onclick="showConfirmation({
                                            title: 'Publish Article',
                                            message: 'Are you ready to publish this article? It will be visible to all users.',
                                            confirmText: 'Publish',
                                            confirmClass: 'btn-primary',
                                            onConfirm: function() { alert('Article published!'); }
                                        })">
                                    <i class="bi bi-globe me-1"></i>Publish
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-secondary w-100"
                                        onclick="showConfirmation({
                                            title: 'Reset Form',
                                            message: 'This will clear all your entered data.',
                                            confirmText: 'Reset',
                                            confirmClass: 'btn-warning',
                                            onConfirm: function() { alert('Form reset!'); }
                                        })">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Form Submission Examples --}}
                    <div class="mb-5">
                        <h5 class="mb-3">2. Form Submission Confirmation</h5>
                        <p class="text-muted">Confirm form submissions with context:</p>

                        <form id="exampleForm" action="#" method="POST" class="mb-3">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" placeholder="Enter some data..." required>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary"
                                            onclick="return confirmFormSubmission(this.form, 'This will submit your form data.', 'Submit Form')">
                                        <i class="bi bi-check-circle me-1"></i>Submit Form
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Leave/Exit Examples --}}
                    <div class="mb-5">
                        <h5 class="mb-3">3. Leave/Exit Confirmation</h5>
                        <p class="text-muted">For actions like leaving teams, groups, or sessions:</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <form action="#" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100"
                                            onclick="return confirmLeave('You will no longer be a member of this team.', null, this.form)">
                                        <i class="bi bi-person-dash me-1"></i>Leave Team
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-warning w-100"
                                        onclick="confirmLeave('Your session will end and unsaved work may be lost.', function() { alert('Session ended!'); })">
                                    <i class="bi bi-box-arrow-right me-1"></i>End Session
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Delete Examples --}}
                    <div class="mb-5">
                        <h5 class="mb-3">4. Delete Confirmation Modal</h5>
                        <p class="text-muted">Specialized delete confirmations with warning styling:</p>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <form action="#" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100"
                                            onclick="return showDeleteConfirmation({
                                                itemName: 'Project Alpha',
                                                form: this.form
                                            })">
                                        <i class="bi bi-trash me-1"></i>Delete Project
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-danger w-100"
                                        onclick="showDeleteConfirmation({
                                            itemName: 'User Account',
                                            onConfirm: function() { alert('Account deleted!'); }
                                        })">
                                    <i class="bi bi-person-x me-1"></i>Delete Account
                                </button>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-danger w-100"
                                        onclick="showDeleteConfirmation({
                                            message: 'All files in this folder will be permanently removed.',
                                            onConfirm: function() { alert('Folder deleted!'); }
                                        })">
                                    <i class="bi bi-folder-x me-1"></i>Delete Folder
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Code Examples --}}
                    <div class="mb-4">
                        <h5 class="mb-3">5. Implementation Examples</h5>

                        <div class="accordion" id="codeExamples">
                            {{-- Basic Confirmation --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="basicExample">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic">
                                        Basic Confirmation Code
                                    </button>
                                </h2>
                                <div id="collapseBasic" class="accordion-collapse collapse" data-bs-parent="#codeExamples">
                                    <div class="accordion-body">
<pre><code>&lt;button onclick="showConfirmation({
    title: 'Confirm Action',
    message: 'Are you sure you want to proceed?',
    confirmText: 'Yes, Continue',
    confirmClass: 'btn-success',
    onConfirm: function() {
        // Your action here
        console.log('Action confirmed!');
    }
})"&gt;Click Me&lt;/button&gt;</code></pre>
                                    </div>
                                </div>
                            </div>

                            {{-- Form Confirmation --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="formExample">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseForm">
                                        Form Submission Code
                                    </button>
                                </h2>
                                <div id="collapseForm" class="accordion-collapse collapse" data-bs-parent="#codeExamples">
                                    <div class="accordion-body">
<pre><code>&lt;form method="POST" action="/submit"&gt;
    @csrf
    &lt;button type="submit"
            onclick="return confirmFormSubmission(this.form, 'Submit this form?', 'Confirm Submission')"&gt;
        Submit Form
    &lt;/button&gt;
&lt;/form&gt;</code></pre>
                                    </div>
                                </div>
                            </div>

                            {{-- Delete Confirmation --}}
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="deleteExample">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDelete">
                                        Delete Confirmation Code
                                    </button>
                                </h2>
                                <div id="collapseDelete" class="accordion-collapse collapse" data-bs-parent="#codeExamples">
                                    <div class="accordion-body">
<pre><code>&lt;form method="POST" action="/delete/item"&gt;
    @csrf
    @method('DELETE')
    &lt;button type="submit"
            onclick="return showDeleteConfirmation({
                itemName: 'Important Document',
                form: this.form
            })"&gt;
        Delete Item
    &lt;/button&gt;
&lt;/form&gt;</code></pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- API Reference --}}
                    <div class="alert alert-info">
                        <h6><i class="bi bi-info-circle me-2"></i>Available Functions</h6>
                        <ul class="mb-0">
                            <li><code>showConfirmation(options)</code> - General purpose confirmation modal</li>
                            <li><code>confirmFormSubmission(form, message, title)</code> - Form submission helper</li>
                            <li><code>confirmLeave(message, onConfirm, form)</code> - Leave action helper</li>
                            <li><code>showDeleteConfirmation(options)</code> - Delete confirmation modal</li>
                            <li><code>confirmDelete(form, itemName, message)</code> - Delete form helper</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
pre {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    font-size: 0.875rem;
    overflow-x: auto;
}

code {
    color: #d63384;
    background-color: #f8f9fa;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

pre code {
    color: inherit;
    background-color: transparent;
    padding: 0;
}
</style>