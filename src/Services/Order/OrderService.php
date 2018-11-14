<?php

/**
 * Service
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Services\Order;

use App\Entity\Item;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ContentService
 * `
 * Object manager of user
 *
 * @package App\Services\Content
 */
class OrderService
{
    private $em;

    /**
     * ContentService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $content
     * @return mixed
     */
    public function save($content)
    {
        // Save user
        $this->em->persist($content);
        $this->em->flush();

        return $content;
    }

    public function formatItems(Order $order)
    {
        $text = trim($order->getItemsText());
        $textAr = explode("\n", $text);
        foreach ($textAr as $line) {
            if (empty(trim($line))) {
                continue;
            }
            $item = new Item();
            $item->setName(trim($line));
            $order->addItem($item);
        }
        $this->save($order);
    }

    /**
     * @param mixed $content
     */
    public function remove($content)
    {
        $this->em->remove($content);
        $this->em->flush();
    }
}
