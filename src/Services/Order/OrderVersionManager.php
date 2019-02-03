<?php

/**
 * Service
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Services\Order;

use App\Entity\Item;
use App\Entity\Order;
use App\Entity\OrderVersion;
use App\Entity\User;
use App\Event\OrderVersionEvent;
use App\OrderEvents;
use App\Repository\OrderVersionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\Registry;

/**
 * @package App\Services\Order
 */
class OrderVersionManager implements OrderVersionManagerInterface
{
    private $em;
    /**
     * @var Registry
     */
    private $workflow;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var ItemManagerInterface
     */
    private $itemManager;

    /** @var \App\Repository\OrderVersionRepository  */
    private $repository;
    /**
     * @var OrderManagerInterface
     */
    private $orderManager;

    /**
     * OrderManager constructor.
     * @param EntityManagerInterface $em
     * @param Registry $workflow
     * @param EventDispatcherInterface $dispatcher
     * @param ItemManagerInterface $itemManager
     * @param OrderManagerInterface $orderManager
     */
    public function __construct(EntityManagerInterface $em, Registry $workflow, EventDispatcherInterface $dispatcher, ItemManagerInterface $itemManager, OrderManagerInterface $orderManager)
    {
        $this->em = $em;
        $this->workflow = $workflow;
        $this->dispatcher = $dispatcher;
        $this->itemManager = $itemManager;
        $this->repository = $this->em->getRepository(OrderVersion::class);
        $this->orderManager = $orderManager;
    }

    /**
     * @param OrderVersion $orderVersion
     * @return OrderVersion
     */
    public function save(OrderVersion $orderVersion): OrderVersion
    {
        $this->em->persist($orderVersion);
        $this->em->flush();

        return $orderVersion;
    }

    /**
     * @param OrderVersion $orderVersion
     */
    public function remove(OrderVersion $orderVersion): void
    {
        $this->em->remove($orderVersion);
        $this->em->flush();
    }

    /**
     * @param $transition
     * @param OrderVersion $orderVersion
     * @param User $user
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function apply($transition, OrderVersion $orderVersion, User $user): void
    {
        if (in_array($transition, OrderVersion::TRANSITION_AVAILABLE)) {
            $this->workflow->get($orderVersion)
                ->apply($orderVersion, $transition);
            $this->save($orderVersion);
            $this->itemManager->checkStateUpdateQuantity($orderVersion);

            $this->dispatcher->dispatch(
                OrderEvents::ORDER_STATE_UPDATED,
                new OrderVersionEvent($orderVersion)
            );
        }
    }

    /**
     * @param OrderVersion $orderVersion
     * @param User $user
     * @return OrderVersion
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createOrderVersion(OrderVersion $orderVersion, User $user): OrderVersion
    {
        $beforeOrderVersion = $this->getBeforeVersion($orderVersion);
        if (null !== $beforeOrderVersion) {
            $this->remove($beforeOrderVersion);
        }
        $orderVersion = clone $orderVersion;
        $orderVersion->setCreator($user);
        $order = $orderVersion->getOrder();
        $order->addVersion($orderVersion);
        $this->orderManager->save($order);

        return $orderVersion;
    }

    public function createFirstOrderVersion(Order $order, User $user): OrderVersion
    {
        $orderVersion = new OrderVersion();
        $orderVersion->setCreator($user);
        $text = trim($order->getItemsText());
        $textAr = explode("\n", $text);
        foreach ($textAr as $line) {
            if (empty(trim($line))) {
                continue;
            }
            $item = new Item();
            $item->setName(trim($line));
            $item->setQuantity(Item::DEFAULT_QUANTITY);
            $orderVersion->addItem($item);
        }
        $order->addVersion($orderVersion);
        $order->setCreator($user);
        $this->orderManager->save($order);

        return $orderVersion;
    }

    /**
     * @param Order $order
     * @return OrderVersion|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastVersion(Order $order): ?OrderVersion
    {
        return $this->getRepository()->getLastVersion($order->getId());
    }

    /**
     * @param OrderVersion $orderVersion
     * @return OrderVersion|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBeforeVersion(OrderVersion $orderVersion): ?OrderVersion
    {
        $id = $this->getRepository()->getMinVersion($orderVersion->getOrder()->getId());
        if ($id === 0 || $orderVersion->getVersion() === $id) {
            return null;
        }

        return $this->getRepository()->getBeforeVersion($orderVersion->getOrder()->getId());
    }

    /**
     * @param OrderVersion $orderVersion
     * @return string|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBeforeState(OrderVersion $orderVersion): ?string
    {
        $beforeVersion = $this->getBeforeVersion($orderVersion);

        return $beforeVersion === null ? null : $beforeVersion->getState();
    }

    public function getRepository(): OrderVersionRepository
    {
        return $this->repository;
    }
}
