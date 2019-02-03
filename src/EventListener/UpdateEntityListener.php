<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 2019-02-02
 * Time: 15:59
 */

namespace App\EventListener;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

class UpdateEntityListener
{
    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        // only act on some "Product" entity
        if ($entity instanceof Order || $entity instanceof User) {
            $entity->setUpdated(new \DateTime());
        }

        return;
    }
}
