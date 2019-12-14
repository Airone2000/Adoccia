<?php

namespace App\EntityListener;

use App\Entity\Fiche;
use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;

final class FicheListener
{
    public function preUpdate(Fiche $fiche, PreUpdateEventArgs $preUpdateEventArgs): void
    {
        $changeSet = $preUpdateEventArgs->getEntityChangeSet();
        $this->deleteOldPicture($changeSet, $preUpdateEventArgs->getEntityManager());
    }

    private function deleteOldPicture(array $changeSet, EntityManagerInterface $entityManager): void
    {
        if (isset($changeSet['picture'])) {
            [$oldPicture] = $changeSet['picture'];
            if ($oldPicture instanceof Picture) {
                $entityManager->remove($oldPicture); // Will flush ...
            }
        }
    }
}
