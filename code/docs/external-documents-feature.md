# External Documents Feature Documentation

## Overview
The External Documents feature allows administrators to upload documents that teams must respond to. This feature includes deadline management, team response tracking, and admin feedback capabilities.

## Feature Highlights
- ✅ Admin document upload with description and metadata
- ✅ Deadline-controlled response periods
- ✅ One response per team per document
- ✅ Admin feedback on team responses
- ✅ File management (upload, download, delete)
- ✅ Multi-language support (Arabic, French, English)
- ✅ Role-based access control

---

## Database Schema

### Tables Created

#### 1. `external_documents`
Stores documents uploaded by administrators.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | Document name/title |
| description | text (nullable) | Optional description |
| file_path | string | Storage path |
| file_original_name | string | Original filename |
| file_size | bigint | File size in bytes |
| file_type | string(10) | File extension (pdf, doc, docx) |
| uploaded_by | foreignId | References users.id (admin) |
| academic_year_id | foreignId (nullable) | References academic_years.id |
| is_active | boolean | Active status (default: true) |
| timestamps | - | created_at, updated_at |

**Indexes:**
- `(academic_year_id, is_active)`
- `uploaded_by`

#### 2. `external_document_responses`
Stores team responses to external documents.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| external_document_id | foreignId | References external_documents.id (cascade) |
| team_id | foreignId | References teams.id (cascade) |
| file_path | string | Storage path |
| file_original_name | string | Original filename |
| file_size | bigint | File size in bytes |
| file_type | string(10) | File extension |
| uploaded_by | foreignId | References users.id (student) |
| admin_feedback | text (nullable) | Admin feedback text |
| feedback_by | foreignId (nullable) | References users.id (admin) |
| feedback_at | timestamp (nullable) | Feedback timestamp |
| timestamps | - | created_at, updated_at |

**Unique Constraint:**
- `(external_document_id, team_id)` - One response per team per document

**Indexes:**
- `external_document_id`
- `team_id`

#### 3. `external_document_deadlines`
Manages upload and response deadline periods.

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | string | Deadline name |
| academic_year | string | Academic year (e.g., "2024-2025") |
| upload_start | datetime (nullable) | Admin upload period start |
| upload_deadline | datetime (nullable) | Admin upload deadline |
| response_start | datetime (nullable) | Team response period start |
| response_deadline | datetime (nullable) | Team response deadline |
| status | enum | draft, active, upload_closed, response_closed, completed |
| description | text (nullable) | Optional description |
| created_by | foreignId | References users.id (admin) |
| timestamps | - | created_at, updated_at |

**Indexes:**
- `academic_year`
- `status`

---

## Models and Relationships

### ExternalDocument Model
**Location:** `app/Models/ExternalDocument.php`

**Relationships:**
- `uploader()` - BelongsTo User (uploaded_by)
- `academicYear()` - BelongsTo AcademicYear
- `responses()` - HasMany ExternalDocumentResponse

**Helper Methods:**
- `getFileSizeHumanAttribute()` - Returns human-readable file size

### ExternalDocumentResponse Model
**Location:** `app/Models/ExternalDocumentResponse.php`

**Relationships:**
- `externalDocument()` - BelongsTo ExternalDocument
- `team()` - BelongsTo Team
- `uploader()` - BelongsTo User (uploaded_by)
- `feedbackProvider()` - BelongsTo User (feedback_by)

**Helper Methods:**
- `hasFeedback()` - Returns boolean
- `getFileSizeHumanAttribute()` - Returns human-readable file size

### ExternalDocumentDeadline Model
**Location:** `app/Models/ExternalDocumentDeadline.php`

**Relationships:**
- `creator()` - BelongsTo User (created_by)

**Helper Methods:**
- `canUploadDocuments()` - Checks if admin can upload now
- `canSubmitResponses()` - Checks if teams can respond now
- `getActive()` - Static method to get active deadline

### Team Model (Updated)
**New Relationship:**
- `externalDocumentResponses()` - HasMany ExternalDocumentResponse

---

## Services

### ExternalDocumentService
**Location:** `app/Services/ExternalDocumentService.php`

**Methods:**

#### Document Management
- `storeDocument(array $data, UploadedFile $file)` - Upload new document
- `updateDocument(ExternalDocument $document, array $data, ?UploadedFile $file)` - Update document
- `deleteDocument(ExternalDocument $document)` - Delete document and responses
- `toggleActive(ExternalDocument $document)` - Toggle active status
- `downloadDocument(ExternalDocument $document)` - Download document file

#### Response Management
- `storeResponse(ExternalDocument $document, array $data, UploadedFile $file)` - Submit team response
- `addFeedback(ExternalDocumentResponse $response, array $data)` - Add admin feedback
- `deleteResponse(ExternalDocumentResponse $response)` - Delete response
- `downloadResponse(ExternalDocumentResponse $response)` - Download response file

#### Retrieval
- `getActiveDocuments(?int $academicYearId)` - Get active documents
- `getDocumentsForTeam(Team $team)` - Get documents with response status for team
- `getDocumentWithResponses(ExternalDocument $document)` - Get document with all responses loaded

---

## Form Request Validators

### StoreExternalDocumentRequest
**Location:** `app/Http/Requests/StoreExternalDocumentRequest.php`

**Authorization:** Admin only

**Validation Rules:**
- name: required, string, max:255
- description: nullable, string, max:1000
- file: required, file, mimes:pdf,doc,docx, max:10240 (10MB)
- academic_year_id: nullable, exists:academic_years,id

**Custom Validation:**
- Checks if active deadline exists
- Checks if upload period is open

### StoreExternalDocumentResponseRequest
**Location:** `app/Http/Requests/StoreExternalDocumentResponseRequest.php`

**Authorization:** Student with team, document must be active

**Validation Rules:**
- file: required, file, mimes:pdf,doc,docx, max:10240 (10MB)

**Custom Validation:**
- Checks if user has a team
- Checks if response period is open
- Checks if team has already responded

### UpdateExternalDocumentFeedbackRequest
**Location:** `app/Http/Requests/UpdateExternalDocumentFeedbackRequest.php`

**Authorization:** Admin only

**Validation Rules:**
- admin_feedback: required, string, min:10, max:2000

---

## Controllers

### Admin\ExternalDocumentController
**Location:** `app/Http/Controllers/Admin/ExternalDocumentController.php`

**Routes:**
- GET `/admin/external-documents` - List all documents
- GET `/admin/external-documents/create` - Show upload form
- POST `/admin/external-documents` - Store new document
- GET `/admin/external-documents/{document}` - View document with responses
- GET `/admin/external-documents/{document}/edit` - Show edit form
- PUT `/admin/external-documents/{document}` - Update document
- DELETE `/admin/external-documents/{document}` - Delete document
- GET `/admin/external-documents/{document}/download` - Download document
- PATCH `/admin/external-documents/{document}/toggle-active` - Toggle active status
- GET `/admin/external-documents/responses/{response}/download` - Download response
- GET `/admin/external-documents/responses/{response}/feedback` - Show feedback form
- POST `/admin/external-documents/responses/{response}/feedback` - Store feedback

### Web\ExternalDocumentController
**Location:** `app/Http/Controllers/Web/ExternalDocumentController.php`

**Routes:**
- GET `/external-documents` - List documents for team
- GET `/external-documents/{document}` - View document and submit response
- GET `/external-documents/{document}/download` - Download document
- POST `/external-documents/{document}/respond` - Submit team response
- GET `/external-documents/{document}/response` - View own response with feedback

---

## Views

### Admin Views
**Location:** `resources/views/admin/external_documents/`

- `index.blade.php` - List all documents with responses count
- `create.blade.php` - Upload new document form
- `show.blade.php` - View document details and all team responses
- `edit.blade.php` - Edit document form
- `feedback.blade.php` - Add feedback to team response

### Student/Team Views
**Location:** `resources/views/web/external_documents/`

- `index.blade.php` - List documents with response status
- `show.blade.php` - View document and response form
- `view_response.blade.php` - View own response with admin feedback
- `no_team.blade.php` - Message when student has no team

---

## User Workflows

### Admin Workflow

1. **Upload Document**
   - Navigate to: Admin → Academic Management → External Documents
   - Click "Upload Document"
   - Fill in: Name, Description (optional), Academic Year, File
   - Submit form
   - Document becomes visible to all teams immediately

2. **View Responses**
   - Click on a document in the list
   - See all team responses
   - View team members, submission date, file info
   - Download individual responses

3. **Provide Feedback**
   - Click "Add Feedback" on a response
   - Write feedback (minimum 10 characters)
   - Submit feedback
   - Team can now see the feedback

4. **Manage Documents**
   - Edit: Update name, description, or replace file
   - Deactivate: Hide document from teams
   - Delete: Permanently remove (with all responses)

### Student/Team Workflow

1. **View Documents**
   - Navigate to: External Documents (in main menu)
   - See list of all active documents
   - Check which documents your team has responded to

2. **Submit Response**
   - Click on a document
   - Download and review the document
   - Upload response file (PDF, DOC, DOCX, max 10MB)
   - Submit (only one response per team allowed)

3. **View Feedback**
   - If admin has provided feedback, it appears on the response page
   - Access via "View Response" button or response details

---

## Deadline Management

### Status Flow
```
draft → active → upload_closed → response_closed → completed
```

### Deadline Model Methods

**For Admins:**
```php
$deadline->canUploadDocuments() // Returns true if:
// - Status is 'active'
// - Current time is between upload_start and upload_deadline
```

**For Teams:**
```php
$deadline->canSubmitResponses() // Returns true if:
// - Status is 'active' or 'upload_closed'
// - Current time is between response_start and response_deadline
```

---

## File Storage

### Storage Disk
Uses Laravel's `public` disk.

### File Paths
- Documents: `storage/app/public/external_documents/`
- Responses: `storage/app/public/external_document_responses/`

### File Validation
- **Allowed formats:** PDF, DOC, DOCX
- **Maximum size:** 10MB
- **Mime type validation:** Enforced

---

## Navigation Menu

### Admin Menu
Location: Academic Management dropdown
```
Academic Management
├── Subjects
├── Teams
├── Defenses
├── Allocations
└── External Documents ← New
```

### Student Menu
Location: Main navigation
```
├── Subjects
├── My Team
└── External Documents ← New
```

---

## Security Features

1. **Authorization**
   - Admin-only document upload/management
   - Student must be in a team to respond
   - Role-based route protection

2. **Validation**
   - File type restrictions
   - File size limits
   - One response per team constraint
   - Deadline enforcement

3. **File Storage**
   - Files stored outside public web root
   - Download through authenticated controllers
   - Secure file deletion

---

## Future Enhancements (Optional)

1. **Notifications**
   - Email/in-app notifications when teams respond
   - Notifications when admin provides feedback

2. **Advanced Features**
   - Document categories/tags
   - Bulk operations
   - Export response summary
   - Document versioning
   - Team collaboration on responses

3. **Analytics**
   - Response rate tracking
   - Average response time
   - Feedback statistics

---

## Migration Commands

To set up the feature, run:

```bash
# Run migrations
php artisan migrate

# If you need to rollback
php artisan migrate:rollback --step=3
```

---

## Testing Checklist

### Admin Testing
- [ ] Upload document successfully
- [ ] Edit document (name, description, file)
- [ ] Delete document
- [ ] Toggle document active/inactive
- [ ] Download document
- [ ] View all responses
- [ ] Download team responses
- [ ] Add feedback to responses
- [ ] Test deadline validation

### Student Testing
- [ ] View documents list
- [ ] Download document
- [ ] Submit response (with valid file)
- [ ] Try to submit duplicate response (should fail)
- [ ] View own response
- [ ] View admin feedback
- [ ] Test without team (should show no-team page)
- [ ] Test with deadline closed (should block submission)

### Edge Cases
- [ ] Upload with invalid file type
- [ ] Upload file exceeding size limit
- [ ] Submit response outside deadline
- [ ] Delete document with responses
- [ ] Multiple teams responding simultaneously

---

## Troubleshooting

### Issue: Files not downloading
**Solution:** Ensure storage link is created:
```bash
php artisan storage:link
```

### Issue: Upload fails silently
**Solution:** Check:
- File permissions on storage directory
- PHP `upload_max_filesize` and `post_max_size` settings
- Laravel file size validation

### Issue: Deadline validation not working
**Solution:** Verify:
- An active deadline exists in database
- Deadline dates are correctly set
- Status is 'active'

---

## API Reference (If Needed)

If you plan to add API endpoints, document them here following your API documentation pattern from `09-Complete-API-Endpoints.md`.

---

## Change Log

### Version 1.0.0 (2025-12-01)
- Initial implementation
- Admin document upload with metadata
- Team response submission
- Admin feedback system
- Deadline management
- Multi-language support

---

## Conclusion

The External Documents feature provides a complete solution for administrators to share documents with teams and collect responses with feedback. The system includes proper validation, deadline management, and role-based access control to ensure secure and organized document management.

For additional support or feature requests, please refer to the main project documentation or contact the development team.
