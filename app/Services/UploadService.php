<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\UploadSession;
use App\Models\UploadFile;
use Carbon\Carbon;
use ZipArchive;
use Illuminate\Support\Facades\Mail;
use App\Mail\UploadReadyMail;
use Illuminate\Support\Facades\Hash;

class UploadService
{
    public function handleUpload(Request $request)
    {
        $request->validate([
            'files' => 'required|array|max:5',
            'files.*' => 'file|max:102400|mimes:jpg,png,pdf,docx,zip',
            'expires_in' => 'nullable|integer|min:1|max:30',
            'email_to_notify' => 'nullable|email',
            'password' => 'nullable|string|min:4|max:32',
        ]);

        $token = (string) Str::uuid();
        $expiresIn = $request->expires_in ?? 1;

        $session = new UploadSession();
        $session->token = $token;
        $session->email_to_notify = $request->email_to_notify ?? null;
        $session->expires_in = $expiresIn;
        $session->expires_at = Carbon::now()->addDays($expiresIn);
        $session->password = $request->password ?? null;

        $session->save();

        foreach ($request->files as $file) {
            $path = Storage::disk('uploads')->putFile('files', $file);

            UploadFile::create([
                'filename' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        if (!empty($request->email_to_notify)) {
            Mail::to($request->email_to_notify)->queue(
                new UploadReadyMail(url("/api/download/{$token}"))
            );
        }

        return [
            'success' => true,
            'download_link' => url("/api/download/{$token}"),
        ];
    }

    public function handleDownload($token)
    {
        $session = UploadSession::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->with('files')
            ->firstOrFail();

        // For Password
        if ($session->password) {
            $request->validate(['password' => 'required|string']);

            if (!Hash::check($request->input('password'), $session->password)) {
                abort(403, 'Incorrect password.');
            }
        }

        // For files
        $files = $session->files;

        if ($files->count() === 1) {
            $file = $files->first();

            if (!Storage::disk('public')->exists($file->filename)) {
                abort(404, 'File not found.');
            }

            $file->increment('download_count');

            return Storage::disk('public')->download($file->filename, $file->original_name);
        }

        // To create a zip of all files
        $zip_name = "download-{$token}.zip";
        $zip_path = storage_path("app/public/zips/{$zip_name}");

        if (!file_exists(dirname($zip_path))) {
            mkdir(dirname($zip_path), 0777, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                if (Storage::disk('public')->exists($file->filename)) {
                    $zip->addFile(storage_path("app/public/{$file->filename}"), $file->original_name);
                    $file->increment('download_count');
                }
            }
            $zip->close();
        } else {
            abort(500, 'Unable to create zip file.');
        }

        return response()->download($zip_path)->deleteFileAfterSend(true);
    }
}
