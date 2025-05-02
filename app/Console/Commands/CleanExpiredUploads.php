<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UploadSession;
use Illuminate\Support\Facades\Storage;

class CleanExpiredUploads extends Command
{
    protected $signature = 'clean:expired-uploads';
    protected $description = 'Delete expired uploads and files';

    public function handle()
    {
        $expired = UploadSession::where('expires_at', '<=', now())->get();

        $this->info("Found {$expired->count()} expired sessions...");

        foreach ($expired as $session) {
            foreach ($session->files as $file) {
                if (Storage::disk('public')->exists($file->filename)) {
                    Storage::disk('public')->delete($file->filename);
                }
            }

            $token = $session->token;
            Storage::disk('public')->deleteDirectory("uploads/{$token}");
            Storage::disk('public')->delete("zips/download-{$token}.zip");

            $session->delete();
        }

        $this->info('Expired uploads deleted.');
    }
}
