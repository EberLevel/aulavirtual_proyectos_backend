<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait FileTrait
{
    private $maxFileSize = 20; // in MB
    private $validImageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    private $validFileExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'zip', 'rar'];
    public function uploadFile($file, $folderName)
    {
        $disk = Storage::disk('public');
        $filename = $file->getClientOriginalName();
        $path = $folderName . '/' . $filename;

        $disk->put($path, file_get_contents($file));

        return $this->getFileUrl($path);
    }
    public function checkIsValidImage($file)
    {
        $fileExtension = $file->getClientOriginalExtension();

        if (in_array($fileExtension, $this->validImageExtensions)) {
            return true;
        }
        if ($file->getSize() <= ($this->maxFileSize * 1024 * 1024)) {
            return true;
        }
        return false;
    }

    public function deleteFile($filePath)
    {
        if (Storage::exists($filePath)) { // Update this line
            Storage::delete($filePath); // Update this line
            return true;
        }
        return false;
    }
    private function getFileUrl($path)
    {
        return url('storage/' . $path);
    }
}
