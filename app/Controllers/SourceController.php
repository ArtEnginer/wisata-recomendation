<?php

namespace App\Controllers;

use App\Models\Akreditasi\AccreditationFile;
use App\Models\Akreditasi\StandardDetail;
use CodeIgniter\Files\Exceptions\FileException;
use CodeIgniter\Files\File;

class SourceController extends BaseController
{
    public function storage(...$path)
    {
        $filepath = WRITEPATH . "uploads" . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $path);

        if (is_dir($filepath)) {
            throw new FileException('Filepath tidak valid');
        }

        if (!is_file($filepath)) {
            throw new FileException('Filepath tidak valid');
        }
        $file = new File($filepath);

        header("Content-Type: {$file->getMimeType()}");
        echo file_get_contents($filepath);
        die;
    }
}
