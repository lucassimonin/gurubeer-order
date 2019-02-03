<?php
/**
 * Created by PhpStorm.
 * User: lsimonin
 * Date: 2018-12-03
 * Time: 14:32
 */

namespace App\Event;

use App\Entity\OrderVersion;
use Symfony\Component\EventDispatcher\Event;

class OrderVersionEvent extends Event
{
    /**
     * @var OrderVersion
     */
    protected $orderVersion;

    /**
     * OrderVersionEvent constructor.
     *
     * @param OrderVersion $orderVersion
     */
    public function __construct(OrderVersion $orderVersion)
    {
        $this->orderVersion = $orderVersion;
    }

    /**
     * @return OrderVersion
     */
    public function getOrderVersion(): OrderVersion
    {
        return $this->orderVersion;
    }
}
