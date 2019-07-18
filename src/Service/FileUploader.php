<?php
/**
 * Created by PhpStorm.
 * User: Utilisateur
 * Date: 18/07/2019
 * Time: 14:49
 */

namespace App\Service;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $photo)
    {
        $originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $photo->move(
                $this->getTargetDirectory(),
                $newFilename
            );
        } catch (FileException $e) {
            $e->getTraceAsString();
        }

        return $newFilename;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

}