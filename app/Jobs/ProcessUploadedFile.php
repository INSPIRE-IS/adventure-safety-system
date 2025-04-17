<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\File;
use App\Models\User;

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
        // $filePath = storage_path("app/{$this->tempPath}");
        // $fileName = $this->data['file_name'];
        // $fileType = $this->data['file_type'];
        File::updateOrCreate(
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

        // Validate the input
        // if (!file_exists($filePath)) {
        //     throw new \Exception("Temporary file not found at $filePath");
        // }

        // $storagePath = 'images/' . $fileName;
        // Storage::disk('public')->put($storagePath, file_get_contents($filePath));
        // $fileUrl = Storage::url($storagePath);

        // File::updateOrCreate(
        //     [
        //         'site_id' => $this->siteId,
        //         'table_name' => $this->data['table_name'],
        //         'table_row_id' => $this->data['table_row_id'],
        //         'type_file_id' => $this->data['type_file_id'],
        //     ],
        //     [
        //         'file_name' => $fileName,
        //         'file_type' => $fileType,
        //         'path' => $fileUrl,
        //     ]
        // );
    }
}
