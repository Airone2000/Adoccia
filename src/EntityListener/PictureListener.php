<?php

namespace App\EntityListener;

use App\Entity\Picture;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class PictureListener
{
    /**
     * @var string
     */
    private $uploadDir;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(string $uploadDir, TokenStorageInterface $tokenStorage)
    {
        $this->uploadDir = $uploadDir;
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(Picture $picture): void
    {
        if ($picture->getUser() === null) {
            $picture->setUser($this->tokenStorage->getToken()->getUser());
        }

        $this->moveUploadedFile($picture);
    }

    public function preUpdate(Picture $picture, PreUpdateEventArgs $args): void
    {
        $this->moveUploadedFile($picture);
    }

    private function moveUploadedFile(Picture $picture): void
    {
        if ($picture->getUploadedFile() instanceof UploadedFile) {

            $file = $picture->getUploadedFile();
            $imageName = uniqid() .'.'. $file->getClientOriginalExtension();

            $file->move(
                $this->uploadDir,
                $imageName
            );

            # Remove old file if exists
            if ($picture->getFilename() !== null) {
                $oldFile = "{$this->uploadDir}/{$picture->getFilename()}";
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }

            # Set new filename
            $picture->setFilename($imageName);

            # Crop and save
            $this->crop($picture);
        }
    }

    private function crop(Picture $picture): void
    {
        if (is_array($picture->getCropCoords())) {
            $imageFilename = "{$this->uploadDir}/{$picture->getFilename()}";
            $croppedImageFilename = "{$this->uploadDir}/crpd_{$picture->getFilename()}";
            $image = $this->imageCreateFromFile($imageFilename, $extension);
            if ($image !== null) {
                $croppedImage = imagecrop($image, $picture->getCropCoords());
                $this->saveImage($croppedImage, $croppedImageFilename, $extension);
                imagedestroy($croppedImage);
            }
        }
    }

    private function saveImage($image, $filename, $extension) {
        switch ($extension) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($image, $filename, 100);
                break;
            case 'png':
                imagepng($image, $filename, 100);
                break;
            case 'gif':
                imagegif($image, $filename);
                break;
        }

        return false;
    }

    private function imageCreateFromFile($filename, &$extension = null)
    {
        switch ( $extension = strtolower( pathinfo( $filename, PATHINFO_EXTENSION )))
        {
            case 'jpeg':
            case 'jpg':
                return imagecreatefromjpeg($filename);
                break;
            case 'png':
                return imagecreatefrompng($filename);
                break;
            case 'gif':
                return imagecreatefromgif($filename);
                break;
        }

        $extension = null;
        return null;
    }
}