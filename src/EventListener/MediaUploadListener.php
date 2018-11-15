<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 20/10/2018
 * Time: 08:50
 */

namespace App\EventListener;

use App\Entity\Order;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use App\Services\Core\FileUploader;

class MediaUploadListener
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Order) {
            return;
        }
        if ($fileName = $entity->getFileName()) {
            $entity->setPdf(new File($this->getFullPathMedia($fileName)));
        }
    }

    private function uploadFile($entity)
    {
        // upload only works for Product entities
        if (!$entity instanceof Order) {
            return;
        }


        $file = $entity->getPdf();

        // only upload new files
        if ($file instanceof UploadedFile) {
            $fileName = $this->uploader->upload($file);
            if (null !== $entity->getFileName()) {
                unlink($this->getFullPathMedia($entity->getFileName()));
            }
            $entity->setFileName($fileName);
        } elseif ($file instanceof File) {
            // prevents the full file path being saved on updates
            // as the path is set on the postLoad listener
            $entity->setFileName($file->getFilename());
        }
    }

    /**
     * @param string $fileName
     * @return string
     */
    private function getFullPathMedia(string $fileName): string
    {
        return $this->uploader->getTargetDirectory().'/'.$fileName;
    }
}
