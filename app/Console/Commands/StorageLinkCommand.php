<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class StorageLinkCommand extends Command
{
    protected $signature = 'storage:link';
    protected $description = 'Create a symbolic link from "public/storage" to "storage/app/public"';

    public function handle()
    {
        $publicPath = base_path('public/storage');
        $storagePath = storage_path('app/public');

        if (File::exists($publicPath)) {
            return $this->error('The "public/storage" directory already exists.');
        }

        File::link($storagePath, $publicPath);

        $this->info('The [public/storage] directory has been linked.');
    }
}