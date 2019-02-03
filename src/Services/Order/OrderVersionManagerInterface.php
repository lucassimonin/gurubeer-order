<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 2019-01-12
 * Time: 12:54
 */

namespace App\Services\Order;

use App\Entity\Order;
use App\Entity\OrderVersion;
use App\Entity\User;
use App\Repository\OrderVersionRepository;

interface OrderVersionManagerInterface
{
    public function save(OrderVersion $order): OrderVersion;
    public function remove(OrderVersion $order): void;
    public function apply($transition, OrderVersion $project, User $user): void;
    public function getRepository(): OrderVersionRepository;
    public function createOrderVersion(OrderVersion $orderVersion, User $user): OrderVersion;
    public function createFirstOrderVersion(Order $order, User $user): OrderVersion;
    public function getLastVersion(Order $order): ?OrderVersion;
    public function getBeforeVersion(OrderVersion $orderVersion): ?OrderVersion;
    public function getBeforeState(OrderVersion $orderVersion): ?string;
}
