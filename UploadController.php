<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UploadSession;
use App\Services\UploadService;
use Illuminate\Http\Request;


class UploadController extends Controller
{
    public function upload(Request $request, UploadService $uploadService)
    {
        $data = $uploadService->handleUpload($request);
        return response()->json($data);
    }

    public function download(Request $request, $token, UploadService $uploadService)
    {
        return $uploadService->handleDownload($request, $token);
    }

    public function stats($token)
    {
        $session = UploadSession::with('files')->where('token', $token)->firstOrFail();

        return response()->json([
            'expires_at' => $session->expires_at,
            'email_to_notify' => $session->email_to_notify,
            'files' => $session->files->map(function ($file) {
                return [
                    'name' => $file->original_name,
                    'size' => $file->size,
                    'downloads' => $file->download_count,
                ];
            }),
        ]);
    }
}
