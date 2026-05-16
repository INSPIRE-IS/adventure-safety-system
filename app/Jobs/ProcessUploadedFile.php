<?php

namespace App\Jobs;

use App\Models\File;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessUploadedFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $userId;
    protected $siteId;

    public function __construct(array $data, int $userId, int $siteId)
    {
        $this->data = $data;
        $this->userId = $userId;
        $this->siteId = $siteId;
    }

    public function handle()
    {
        // set env var MOBILE_UPLOAD_DEBUG=true to enable debug logging
        if (config('app.mobile_upload_debug')) {
            Log::info('mobile.files.job.start', [
                'user_id' => $this->userId,
                'site_id' => $this->siteId,
                'table_name' => $this->data['table_name'] ?? null,
                'table_row_id' => $this->data['table_row_id'] ?? null,
                'type_file_id' => $this->data['type_file_id'] ?? null,
                'file_name' => $this->data['file_name'] ?? null,
            ]);
        }

        $file = File::updateOrCreate(
            [
                'site_id' => $this->siteId,
                'table_name' => $this->data['table_name'],
                'table_row_id' => $this->data['table_row_id'],
                'type_file_id' => $this->data['type_file_id'],
            ],
            [
                'file_name' => $this->data['file_name'],
                'file_type' => $this->data['file_type'],
                'path' => $this->data['path'],
            ]
        );

        if (config('app.mobile_upload_debug')) {
            Log::info('mobile.files.job.stored', [
                'id' => (int) $file->id,
                'was_recently_created' => (bool) $file->wasRecentlyCreated,
                'site_id' => (int) $file->site_id,
                'table_name' => (string) $file->table_name,
                'table_row_id' => (int) $file->table_row_id,
                'type_file_id' => (int) $file->type_file_id,
                'path' => (string) $file->path,
            ]);
        }
    }
}
