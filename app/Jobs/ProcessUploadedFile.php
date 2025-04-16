<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
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
        $base64String = $this->data['file_base64'];
        $imageData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64String));

        $fileName = 'images/' . $this->data['file_name'];

        Storage::disk('public')->put($fileName, $imageData);
        $filePath = Storage::url($fileName);
        $user = User::find($this->userId);
        //$siteId = $user->teams->first()->id; // auth()->loginUsingId($this->userId)


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
                'path' => $filePath,
            ]
        );
    }
}
