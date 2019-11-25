<?php

namespace App\EntityListener;

use App\Entity\Picture;

final class PictureListener
{
    /**
     * @var string
     */
    private $pictureUploadDir;

    public function __construct(string $pictureUploadDir)
    {
        $this->pictureUploadDir = $pictureUploadDir;
    }

    function preRemove(Picture $picture)
    {
        $fileToDelete = $this->pictureUploadDir . DIRECTORY_SEPARATOR . $picture->getFilename();
        if (file_exists($fileToDelete)) {
            @unlink($fileToDelete);
        }
    }
}