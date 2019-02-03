<?php

/**
 * Service
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Services\Order;

use App\Entity\Item;
use App\Entity\OrderVersion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package App\Services\Order
 */
class ItemManager implements ItemManagerInterface
{
    private $em;
    private $originalItems;

    /**
     * OrderManager constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->originalItems = new ArrayCollection();
    }

    /**
     * @param Item $item
     * @return Item
     */
    public function save(Item $item): Item
    {
        $this->em->persist($item);
        $this->em->flush();

        return $item;
    }

    /**
     * @param Item $item
     */
    public function remove(Item $item): void
    {
        $this->em->remove($item);
        $this->em->flush();
    }

    public function removeOldItems(OrderVersion $orderVersion): OrderVersion
    {
        foreach ($this->originalItems as $item) {
            if (false === $orderVersion->getItems()->contains($item)) {
                $orderVersion->removeItem($item);
                $this->remove($item);
            }
        }

        return $orderVersion;
    }

    public function checkStateUpdateQuantity(OrderVersion $orderVersion): void
    {
        /** @var Item $item */
        foreach ($orderVersion->getItems() as $item) {
            $this->checkState($item);
        }
    }

    private function checkState(Item $item): void
    {
        if ($item->getQuantityUpdated() !== $item->getQuantity()) {
            if ($item->getState() !== Item::STATE_ADDED || 0 !== $item->getQuantity()) {
                $item->setState(Item::STATE_UPDATED);
            }
        } else {
            $item->setState(Item::STATE_NO_CHANGE);
        }
        $item->setQuantity($item->getQuantityUpdated());
        $this->save($item);
    }

    public function setOriginalItems(OrderVersion $orderVersion): void
    {
        /** @var Item $thematic */
        foreach ($orderVersion->getItems() as $item) {
            $this->originalItems->add($item);
        }
    }

    public function addOriginalItems(OrderVersion &$orderVersion): void
    {
        /** @var Item $item */
        foreach ($orderVersion->getItems() as $item) {
            if ($item->getState() === Item::STATE_ADDED) {
                $orderVersion->removeItem($item);
                $this->remove($item);
            }
        }
        /** @var Item $item */
        foreach ($this->originalItems as $item) {
            $orderVersion->addItem($item);
        }
    }
}
