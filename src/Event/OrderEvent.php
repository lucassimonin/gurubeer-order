<?php
/**
 * Created by PhpStorm.
 * User: lsimonin
 * Date: 2018-12-03
 * Time: 14:32
 */

namespace App\Event;

use App\Entity\Order;
use Symfony\Component\EventDispatcher\Event;

class OrderEvent extends Event
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * ProjectStateUpdatedEvent constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }
}
