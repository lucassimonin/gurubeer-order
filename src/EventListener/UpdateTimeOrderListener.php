<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 20/10/2018
 * Time: 08:50
 */

namespace App\EventListener;

use App\Event\OrderVersionEvent;
use App\OrderEvents;
use App\Services\Order\OrderManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UpdateTimeOrderListener implements EventSubscriberInterface
{
    /**
     * @var OrderManagerInterface
     */
    private $orderManager;

    /**
     * UpdateTimeOrderListener constructor.
     * @param OrderManagerInterface $orderManager
     */
    public function __construct(OrderManagerInterface $orderManager)
    {
        $this->orderManager = $orderManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            OrderEvents::ORDER_STATE_UPDATED => 'UpdateTimeOrder'
        ];
    }

    /**
     * @param OrderVersionEvent $orderVersionEvent
     * @throws \Exception
     */
    public function UpdateTimeOrder(OrderVersionEvent $orderVersionEvent)
    {
        $order =$orderVersionEvent->getOrderVersion()->getOrder();
        $order->setUpdated(new \DateTime());
        $this->orderManager->save($order);
    }
}
