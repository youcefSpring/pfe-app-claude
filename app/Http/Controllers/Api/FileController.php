<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PFE\FileUploadRequest;
use App\Services\FileManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function __construct(private FileManagementService $fileService)
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Upload a file
     */
    public function upload(FileUploadRequest $request): JsonResponse
    {
        $user = $request->user();
        $file = $request->file('file');
        $type = $request->type;

        try {
            switch ($type) {
                case 'avatar':
                    $filePath = $this->fileService->uploadAvatar($file, $user);
                    $user->update(['avatar_path' => $filePath]);
                    break;

                case 'deliverable':
                    if (!$request->project_id) {
                        return response()->json([
                            'error' => 'Project ID required',
                            'message' => 'Project ID is required for deliverable uploads'
                        ], 422);
                    }

                    $project = \App\Models\PfeProject::findOrFail($request->project_id);
                    $fileData = $this->fileService->uploadDeliverable($file, $project, $user);
                    $filePath = $fileData['file_path'];
                    break;

                default:
                    return response()->json([
                        'error' => 'Invalid Type',
                        'message' => 'Invalid file type specified'
                    ], 422);
            }

            return response()->json([
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'message' => 'File uploaded successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Upload Failed',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Download or view a file
     */
    public function download(Request $request, string $path): Response
    {
        $user = $request->user();

        // Decode the path
        $filePath = base64_decode($path);

        // Check if user can access this file
        $fullPath = $this->fileService->downloadFile($filePath, $user);

        if (!$fullPath) {
            abort(404, 'File not found or access denied');
        }

        // Check if file exists
        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found');
        }

        // Get file info
        $fileInfo = $this->fileService->getFileInfo($filePath);

        // Return file response
        return response()->file($fullPath, [
            'Content-Type' => $fileInfo['mime_type'],
            'Content-Length' => $fileInfo['size']
        ]);
    }

    /**
     * Get file information
     */
    public function info(Request $request, string $path): JsonResponse
    {
        $user = $request->user();
        $filePath = base64_decode($path);

        // Check access permissions
        if (!$this->fileService->downloadFile($filePath, $user)) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'You do not have permission to access this file'
            ], 403);
        }

        $fileInfo = $this->fileService->getFileInfo($filePath);

        if (!$fileInfo) {
            return response()->json([
                'error' => 'Not Found',
                'message' => 'File not found'
            ], 404);
        }

        return response()->json([
            'file_info' => [
                'path' => $filePath,
                'size' => $fileInfo['size'],
                'size_human' => $this->formatBytes($fileInfo['size']),
                'mime_type' => $fileInfo['mime_type'],
                'last_modified' => date('Y-m-d H:i:s', $fileInfo['last_modified'])
            ]
        ]);
    }

    /**
     * Delete a file
     */
    public function destroy(Request $request, string $path): JsonResponse
    {
        $user = $request->user();
        $filePath = base64_decode($path);

        $deleted = $this->fileService->deleteFile($filePath, $user);

        if (!$deleted) {
            return response()->json([
                'error' => 'Cannot Delete',
                'message' => 'File not found or you do not have permission to delete it'
            ], 403);
        }

        return response()->json([
            'message' => 'File deleted successfully'
        ]);
    }

    /**
     * Generate secure temporary URL for file access
     */
    public function generateSecureUrl(Request $request, string $path): JsonResponse
    {
        $user = $request->user();
        $filePath = base64_decode($path);

        // Check access permissions
        if (!$this->fileService->downloadFile($filePath, $user)) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'You do not have permission to access this file'
            ], 403);
        }

        $expirationMinutes = $request->get('expires_in', 60); // Default 1 hour
        $secureUrl = $this->fileService->generateSecureUrl($filePath, $expirationMinutes);

        return response()->json([
            'secure_url' => $secureUrl,
            'expires_at' => now()->addMinutes($expirationMinutes)->toISOString()
        ]);
    }

    /**
     * Get storage usage statistics (admin only)
     */
    public function storageStats(): JsonResponse
    {
        $this->authorize('viewStorageStats', FileController::class);

        $stats = $this->fileService->getStorageUsage();

        return response()->json([
            'storage_stats' => [
                'total_size_bytes' => $stats['total_size'],
                'total_size_mb' => $stats['total_size_mb'],
                'total_size_human' => $this->formatBytes($stats['total_size']),
                'file_count' => $stats['file_count'],
                'available_space_bytes' => $stats['available_space'],
                'available_space_human' => $this->formatBytes($stats['available_space'])
            ]
        ]);
    }

    /**
     * Clean up old temporary files (admin only)
     */
    public function cleanup(Request $request): JsonResponse
    {
        $this->authorize('cleanupFiles', FileController::class);

        $daysOld = $request->get('days_old', 30);
        $deletedCount = $this->fileService->cleanupOldFiles($daysOld);

        return response()->json([
            'deleted_count' => $deletedCount,
            'message' => "Cleaned up {$deletedCount} old files"
        ]);
    }

    /**
     * Scan file for viruses (if virus scanning is enabled)
     */
    public function scanFile(Request $request, string $path): JsonResponse
    {
        $user = $request->user();
        $filePath = base64_decode($path);

        // Check access permissions
        if (!$this->fileService->downloadFile($filePath, $user)) {
            return response()->json([
                'error' => 'Access Denied',
                'message' => 'You do not have permission to access this file'
            ], 403);
        }

        $isClean = $this->fileService->scanForViruses($filePath);

        return response()->json([
            'file_path' => $filePath,
            'scan_result' => $isClean ? 'clean' : 'infected',
            'is_safe' => $isClean
        ]);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}