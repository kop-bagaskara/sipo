<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GoogleDriveService
{
    private $apiKey;
    private $baseUrl = 'https://www.googleapis.com/drive/v3';

    public function __construct()
    {
        // Ambil API key dari .env
        $this->apiKey = env('GOOGLE_DRIVE_API_KEY');

        if (!$this->apiKey) {
            Log::warning('Google Drive API Key tidak ditemukan di .env');
        }
    }

    /**
     * Get file metadata from Google Drive
     *
     * @param string $fileId Google Drive file ID
     * @return array|null Returns array with 'success' and 'data' or 'error' keys
     */
    public function getFileMetadata($fileId)
    {
        if (!$this->apiKey) {
            return ['success' => false, 'error' => 'API Key tidak dikonfigurasi'];
        }

        try {
            $response = Http::get("{$this->baseUrl}/files/{$fileId}", [
                'key' => $this->apiKey,
                'fields' => 'id,name,mimeType,size,webViewLink,webContentLink'
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            // Parse error message
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();
            $errorCode = $errorBody['error']['code'] ?? $response->status();

            Log::error('Google Drive API Error: ' . $errorMessage);

            return [
                'success' => false,
                'error' => $errorMessage,
                'error_code' => $errorCode
            ];
        } catch (\Exception $e) {
            Log::error('Google Drive Service Error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get direct download URL for video streaming
     * Google Drive files can be downloaded using the webContentLink or by constructing a direct link
     *
     * @param string $fileId Google Drive file ID
     * @return string|null
     */
    public function getStreamUrl($fileId)
    {
        if (!$this->apiKey) {
            return null;
        }

        // Untuk video streaming, kita perlu menggunakan route Laravel yang akan proxy stream
        // Ini memungkinkan kontrol penuh (prevent skip, speed control, dll)
        return route('hr.portal-training.video.stream', ['fileId' => $fileId]);
    }

    /**
     * Stream video from Google Drive
     * This method downloads and streams the video chunk by chunk to allow control
     *
     * @param string $fileId Google Drive file ID
     * @param int|null $startByte For range requests (video seeking)
     * @param int|null $endByte For range requests
     * @return \Illuminate\Http\Response
     */
    public function streamVideo($fileId, $startByte = null, $endByte = null)
    {
        if (!$this->apiKey) {
            abort(500, 'Google Drive API Key tidak dikonfigurasi');
        }

        try {
            // Get file metadata first
            $fileResult = $this->getFileMetadata($fileId);

            if (!$fileResult['success'] || !isset($fileResult['data'])) {
                $errorMessage = $fileResult['error'] ?? 'File tidak ditemukan';
                abort(404, 'File tidak dapat diakses: ' . $errorMessage);
            }

            $metadata = $fileResult['data'];

            // Construct download URL
            // Google Drive allows direct download with API key
            $downloadUrl = "{$this->baseUrl}/files/{$fileId}?alt=media&key={$this->apiKey}";

            // Prepare headers for streaming
            $headers = [
                'Accept' => 'video/*',
            ];

            // Handle range requests (for video seeking)
            if ($startByte !== null || $endByte !== null) {
                $rangeHeader = 'bytes=';
                if ($startByte !== null) {
                    $rangeHeader .= $startByte;
                }
                $rangeHeader .= '-';
                if ($endByte !== null) {
                    $rangeHeader .= $endByte;
                }
                $headers['Range'] = $rangeHeader;
            }

            // Stream video from Google Drive
            $response = Http::withHeaders($headers)
                ->timeout(300) // 5 minutes timeout
                ->get($downloadUrl);

            if ($response->successful() || $response->status() === 206) { // 206 = Partial Content
                $contentType = $metadata['mimeType'] ?? 'video/mp4';

                return response($response->body(), $response->status(), [
                    'Content-Type' => $contentType,
                    'Content-Length' => $response->header('Content-Length') ?? strlen($response->body()),
                    'Accept-Ranges' => 'bytes',
                    'Cache-Control' => 'public, max-age=3600',
                ]);
            }

            abort(500, 'Gagal mengambil video dari Google Drive');
        } catch (\Exception $e) {
            Log::error('Google Drive Stream Error: ' . $e->getMessage());
            abort(500, 'Terjadi kesalahan saat streaming video');
        }
    }

    /**
     * Extract Google Drive file ID from URL
     * Supports various Google Drive URL formats
     *
     * @param string $url Google Drive URL
     * @return string|null
     */
    public static function extractFileId($url)
    {
        // Format 1: https://drive.google.com/file/d/FILE_ID/view
        // Format 2: https://drive.google.com/open?id=FILE_ID
        // Format 3: https://drive.google.com/uc?id=FILE_ID
        // Format 4: FILE_ID (direct ID)

        // Jika sudah berupa ID langsung
        if (preg_match('/^[a-zA-Z0-9_-]+$/', $url)) {
            return $url;
        }

        // Extract dari URL
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Test Google Drive API connection
     *
     * @param string|null $testFileId Optional file ID to test with specific file
     * @return array
     */
    public function testConnection($testFileId = null)
    {
        $result = [
            'success' => false,
            'api_key_configured' => false,
            'api_key_valid' => false,
            'drive_api_accessible' => false,
            'test_file_accessible' => false,
            'message' => '',
            'details' => []
        ];

        // Check if API key is configured
        if (!$this->apiKey) {
            $result['message'] = 'Google Drive API Key tidak ditemukan di .env';
            return $result;
        }

        $result['api_key_configured'] = true;
        $result['details'][] = 'API Key ditemukan di .env';

        // Test basic API access (list files with limit 1)
        // This test is independent of file ID - it only tests if API key works
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/files", [
                'key' => $this->apiKey,
                'pageSize' => 1,
                'fields' => 'files(id,name)'
            ]);

            if ($response->successful()) {
                $result['api_key_valid'] = true;
                $result['drive_api_accessible'] = true;
                $result['details'][] = 'API Key valid dan Google Drive API dapat diakses';
            } else {
                $errorBody = $response->json();
                $errorMessage = $errorBody['error']['message'] ?? $response->body();
                $errorCode = $errorBody['error']['code'] ?? $response->status();
                $errorReason = $errorBody['error']['errors'][0]['reason'] ?? null;

                // Detailed error analysis
                $result['error_details'] = [
                    'message' => $errorMessage,
                    'code' => $errorCode,
                    'reason' => $errorReason,
                    'status' => $response->status()
                ];

                // Provide specific guidance based on error
                $diagnosis = $this->diagnoseApiError($errorMessage, $errorCode, $errorReason);
                $result['diagnosis'] = $diagnosis;

                // This is a real API key or API access error
                $result['message'] = 'API Key tidak valid atau tidak memiliki akses: ' . $errorMessage;
                $result['details'][] = 'Error: ' . $errorMessage;
                $result['details'][] = 'Diagnosis: ' . $diagnosis['title'];
                if (!empty($diagnosis['solutions'])) {
                    foreach ($diagnosis['solutions'] as $solution) {
                        $result['details'][] = 'Solusi: ' . $solution;
                    }
                }
                return $result;
            }
        } catch (\Exception $e) {
            $result['message'] = 'Gagal mengakses Google Drive API: ' . $e->getMessage();
            $result['details'][] = 'Exception: ' . $e->getMessage();
            return $result;
        }

        // Test with specific file if provided
        if ($testFileId) {
            try {
                $fileResult = $this->getFileMetadata($testFileId);

                if ($fileResult['success'] && isset($fileResult['data'])) {
                    $fileMetadata = $fileResult['data'];
                    $result['test_file_accessible'] = true;
                    $result['details'][] = "File test dapat diakses: {$fileMetadata['name']} (ID: {$fileMetadata['id']})";
                    $result['test_file_info'] = [
                        'id' => $fileMetadata['id'],
                        'name' => $fileMetadata['name'],
                        'mimeType' => $fileMetadata['mimeType'] ?? 'unknown',
                        'size' => $fileMetadata['size'] ?? 'unknown',
                    ];
                } else {
                    $errorMessage = $fileResult['error'] ?? 'Unknown error';
                    $result['test_file_accessible'] = false;
                    $result['details'][] = "File test error: {$errorMessage}";

                    // Check if it's a permission error
                    if (stripos($errorMessage, 'permission') !== false || stripos($errorMessage, 'sufficient permissions') !== false) {
                        $result['file_permission_error'] = true;
                        $result['message'] = "File dengan ID '{$testFileId}' tidak dapat diakses karena masalah permission. " .
                            "Pastikan file sudah di-share dengan permission 'Anyone with the link' atau 'Public' di Google Drive.";
                        $result['details'][] = "Solusi: Buka file di Google Drive > Klik kanan > Share > Ubah permission ke 'Anyone with the link'";
                    } else {
                        $result['message'] = "File dengan ID '{$testFileId}' tidak dapat diakses: {$errorMessage}";
                    }
                }
            } catch (\Exception $e) {
                $result['details'][] = 'Error saat test file: ' . $e->getMessage();
                $result['message'] = "Error saat mengakses file: " . $e->getMessage();
            }
        }

        // If all basic tests pass (file permission error doesn't count as API failure)
        if ($result['api_key_configured'] && $result['api_key_valid'] && $result['drive_api_accessible']) {
            $result['success'] = true;

            // If file test failed but API works, show appropriate message
            if (isset($testFileId) && !$result['test_file_accessible']) {
                if (isset($result['file_permission_error']) && $result['file_permission_error']) {
                    // Don't override the permission error message
                    // Keep success = true because API itself works
                } else {
                    $result['message'] = 'Google Drive API berfungsi dengan baik! Namun file test tidak dapat diakses.';
                }
            } else if (empty($result['message'])) {
                $result['message'] = 'Google Drive API berfungsi dengan baik!';
            }
        }

        return $result;
    }

    /**
     * Diagnose API error and provide specific solutions
     *
     * @param string $errorMessage
     * @param int|null $errorCode
     * @param string|null $errorReason
     * @return array
     */
    private function diagnoseApiError($errorMessage, $errorCode = null, $errorReason = null)
    {
        $diagnosis = [
            'title' => 'Error tidak diketahui',
            'solutions' => []
        ];

        // Check for common API key errors
        if (stripos($errorMessage, 'API key not valid') !== false ||
            stripos($errorMessage, 'invalid API key') !== false ||
            $errorCode == 400) {
            $diagnosis['title'] = 'API Key tidak valid';
            $diagnosis['solutions'] = [
                'Pastikan API Key yang dimasukkan di .env sudah benar (copy-paste langsung dari Google Cloud Console)',
                'Cek apakah ada spasi atau karakter tambahan di awal/akhir API Key',
                'Pastikan API Key belum expired atau dihapus',
                'Buat API Key baru di Google Cloud Console jika perlu'
            ];
        } elseif (stripos($errorMessage, 'API key not authorized') !== false ||
                   stripos($errorMessage, 'API has not been used') !== false ||
                   stripos($errorMessage, 'not enabled') !== false ||
                   $errorCode == 403) {
            $diagnosis['title'] = 'API Key tidak memiliki akses ke Google Drive API atau API Key memiliki restriction';
            $diagnosis['solutions'] = [
                'Pastikan Anda menggunakan API Key (bukan OAuth Client ID)',
                'Buka Google Cloud Console > APIs & Services > Credentials',
                'Cari API Key Anda (bukan OAuth 2.0 Client ID)',
                'Klik Edit pada API Key',
                'Di bagian "API restrictions", pastikan:',
                '  - Pilih "Restrict key"',
                '  - Tambahkan "Google Drive API" ke daftar allowed APIs',
                '  - ATAU pilih "Don\'t restrict key" untuk testing (tidak disarankan untuk production)',
                'Klik Save dan tunggu beberapa menit',
                'Test lagi'
            ];
        } elseif (stripos($errorMessage, 'API key expired') !== false) {
            $diagnosis['title'] = 'API Key sudah expired';
            $diagnosis['solutions'] = [
                'Buat API Key baru di Google Cloud Console',
                'Update API Key di file .env',
                'Restart aplikasi jika perlu'
            ];
        } elseif (stripos($errorMessage, 'quota') !== false ||
                  stripos($errorMessage, 'rate limit') !== false) {
            $diagnosis['title'] = 'Quota API sudah habis';
            $diagnosis['solutions'] = [
                'Cek quota di Google Cloud Console > APIs & Services > Dashboard',
                'Tunggu beberapa saat atau upgrade quota jika perlu'
            ];
        } elseif ($errorCode == 401) {
            $diagnosis['title'] = 'Unauthorized - API Key tidak valid atau tidak memiliki permission';
            $diagnosis['solutions'] = [
                'Pastikan API Key sudah benar',
                'Pastikan Google Drive API sudah di-enable',
                'Cek apakah API Key memiliki restriction yang menghalangi akses'
            ];
        } else {
            $diagnosis['title'] = 'Error tidak diketahui: ' . $errorMessage;
            $diagnosis['solutions'] = [
                'Cek log Laravel untuk detail error',
                'Pastikan koneksi internet server stabil',
                'Coba test lagi setelah beberapa saat'
            ];
        }

        return $diagnosis;
    }
}

