<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\PfeProject;

class FileManagementService
{
    private const ALLOWED_DOCUMENT_TYPES = ['pdf', 'doc', 'docx'];
    private const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'gif'];
    private const MAX_FILE_SIZE = 10485760; // 10MB
    private const MAX_IMAGE_SIZE = 2097152; // 2MB

    public function uploadDeliverable(UploadedFile $file, PfeProject $project, User $uploader): array
    {
        $this->validateFile($file, self::ALLOWED_DOCUMENT_TYPES, self::MAX_FILE_SIZE);

        $fileName = $this->generateSecureFileName($file, 'deliverable');
        $path = "deliverables/{$project->id}/{$fileName}";

        Storage::disk('private')->put($path, file_get_contents($file));

        return [
            'file_path' => $path,
            'file_name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_type' => $file->getClientOriginalExtension(),
            'uploaded_by' => $uploader->id,
            'uploaded_at' => now()
        ];
    }

    public function uploadAvatar(UploadedFile $file, User $user): string
    {
        $this->validateFile($file, self::ALLOWED_IMAGE_TYPES, self::MAX_IMAGE_SIZE);

        // Delete old avatar if exists
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $fileName = $this->generateSecureFileName($file, 'avatar');
        $path = "avatars/{$fileName}";

        // Resize and optimize image
        $resizedImage = $this->resizeImage($file, 200, 200);
        Storage::disk('public')->put($path, $resizedImage);

        return $path;
    }

    public function uploadPvDocument(array $data, int $defenseId): string
    {
        $fileName = "pv_defense_{$defenseId}_" . now()->format('Y-m-d_H-i-s') . '.pdf';
        $path = "pv_documents/{$fileName}";

        // Generate PDF from data (this would use a PDF library like TCPDF or DomPDF)
        $pdfContent = $this->generatePvPdf($data);
        Storage::disk('private')->put($path, $pdfContent);

        return $path;
    }

    public function downloadFile(string $filePath, User $requester): ?string
    {
        if (!$this->canAccessFile($filePath, $requester)) {
            return null;
        }

        if (!Storage::disk('private')->exists($filePath)) {
            return null;
        }

        return Storage::disk('private')->path($filePath);
    }

    public function deleteFile(string $filePath, User $requester): bool
    {
        if (!$this->canDeleteFile($filePath, $requester)) {
            return false;
        }

        return Storage::disk('private')->delete($filePath);
    }

    public function getFileInfo(string $filePath): ?array
    {
        if (!Storage::disk('private')->exists($filePath)) {
            return null;
        }

        return [
            'size' => Storage::disk('private')->size($filePath),
            'last_modified' => Storage::disk('private')->lastModified($filePath),
            'mime_type' => Storage::disk('private')->mimeType($filePath)
        ];
    }

    public function scanForViruses(string $filePath): bool
    {
        // Placeholder for virus scanning integration
        // This would integrate with ClamAV or similar
        return true;
    }

    public function generateSecureUrl(string $filePath, int $expirationMinutes = 60): string
    {
        return Storage::disk('private')->temporaryUrl(
            $filePath,
            now()->addMinutes($expirationMinutes)
        );
    }

    public function cleanupOldFiles(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);
        $deletedCount = 0;

        // Clean up temporary files
        $tempFiles = Storage::disk('private')->allFiles('temp');
        foreach ($tempFiles as $file) {
            $lastModified = Storage::disk('private')->lastModified($file);
            if ($lastModified < $cutoffDate->timestamp) {
                Storage::disk('private')->delete($file);
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    public function getStorageUsage(): array
    {
        $totalSize = 0;
        $fileCount = 0;

        $allFiles = Storage::disk('private')->allFiles();
        foreach ($allFiles as $file) {
            $totalSize += Storage::disk('private')->size($file);
            $fileCount++;
        }

        return [
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'file_count' => $fileCount,
            'available_space' => disk_free_space(Storage::disk('private')->path(''))
        ];
    }

    private function validateFile(UploadedFile $file, array $allowedTypes, int $maxSize): void
    {
        $errors = ValidationHelper::validateFileUpload([
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize()
        ], $allowedTypes, $maxSize);

        if (!empty($errors)) {
            throw new \InvalidArgumentException(implode(', ', $errors));
        }

        // Additional security checks
        if (!$file->isValid()) {
            throw new \InvalidArgumentException('File upload failed');
        }
    }

    private function generateSecureFileName(UploadedFile $file, string $prefix): string
    {
        $extension = $file->getClientOriginalExtension();
        $hash = hash('sha256', $file->getClientOriginalName() . time());
        $randomString = Str::random(8);

        return "{$prefix}_{$randomString}_{$hash}.{$extension}";
    }

    private function resizeImage(UploadedFile $file, int $width, int $height): string
    {
        // This would use an image manipulation library like Intervention Image
        // For now, return the original file content
        return file_get_contents($file);
    }

    private function generatePvPdf(array $data): string
    {
        // This would use a PDF generation library
        // For now, return a placeholder
        return "PDF content for defense PV";
    }

    private function canAccessFile(string $filePath, User $user): bool
    {
        // Implement file access control based on user roles and file type

        // Extract file type from path
        if (str_contains($filePath, 'deliverables/')) {
            return $this->canAccessDeliverable($filePath, $user);
        }

        if (str_contains($filePath, 'pv_documents/')) {
            return $this->canAccessPvDocument($filePath, $user);
        }

        if (str_contains($filePath, 'avatars/')) {
            return true; // Avatars are generally accessible
        }

        return false;
    }

    private function canAccessDeliverable(string $filePath, User $user): bool
    {
        // Extract project ID from path: deliverables/{project_id}/{filename}
        $pathParts = explode('/', $filePath);
        if (count($pathParts) < 3) {
            return false;
        }

        $projectId = $pathParts[1];
        $project = PfeProject::find($projectId);

        if (!$project) {
            return false;
        }

        // Allow access to:
        // - Team members
        // - Supervisor
        // - Jury members (if defense is scheduled)
        // - Admin users
        return $user->hasRole('admin_pfe') ||
               $project->supervisor_id === $user->id ||
               $project->team->members()->where('user_id', $user->id)->exists() ||
               ($project->defense && in_array($user->id, [
                   $project->defense->jury_president_id,
                   $project->defense->jury_examiner_id,
                   $project->defense->jury_supervisor_id
               ]));
    }

    private function canAccessPvDocument(string $filePath, User $user): bool
    {
        // PV documents are accessible to:
        // - Team members (after defense)
        // - Jury members
        // - Admin users
        return $user->hasRole(['admin_pfe', 'chef_master']) ||
               $this->isUserInvolvedInDefense($filePath, $user);
    }

    private function canDeleteFile(string $filePath, User $user): bool
    {
        // Only admins and file owners can delete files
        return $user->hasRole('admin_pfe') ||
               $this->isFileOwner($filePath, $user);
    }

    private function isUserInvolvedInDefense(string $filePath, User $user): bool
    {
        // Extract defense ID from PV filename and check if user is involved
        // This would require more complex logic based on the actual filename format
        return false;
    }

    private function isFileOwner(string $filePath, User $user): bool
    {
        // Check if user is the original uploader
        // This would require storing file ownership information
        return false;
    }
}