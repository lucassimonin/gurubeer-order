<?php

/**
 * Service
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Services\Order;

use App\Entity\Order;
use App\Event\OrderEvent;
use App\OrderEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @package App\Services\Order
 */
class OrderManager implements OrderManagerInterface
{
    private $em;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * OrderManager constructor.
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Order $order
     * @return Order
     */
    public function save(Order $order): Order
    {
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

    /**
     * @param Order $order
     */
    public function remove(Order $order): void
    {
        $this->em->remove($order);
        $this->em->flush();
        $this->dispatcher->dispatch(
            OrderEvents::ORDER_DELETE,
            new OrderEvent($order)
        );
    }
}
