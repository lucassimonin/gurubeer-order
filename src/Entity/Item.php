<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 12/11/2018
 * Time: 18:50
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="gurubeer_item")
 * @ORM\Entity()
 */
class Item
{
    public const TYPE_BARREL = 'barrel';
    public const TYPE_BOTTLE = 'bottle';
    public const DEFAULT_QUANTITY = 24;

    public const STATE_UPDATED = 'updated';
    public const STATE_NO_CHANGE = 'no_change';
    public const STATE_ADDED = 'added';

    public const LIST_TYPE = [
        self::TYPE_BOTTLE => 'admin.type.' . self::TYPE_BOTTLE,
        self::TYPE_BARREL => 'admin.type.' . self::TYPE_BARREL
    ];
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="items", fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     * @var Order|null
     */
    private $order;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $state = self::STATE_ADDED;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $type = self::TYPE_BOTTLE;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $quantity = self::DEFAULT_QUANTITY;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $quantityUpdated = self::DEFAULT_QUANTITY;


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param null|string $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     */
    public function setOrder(?Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
        $this->quantityUpdated = $quantity;
    }

    /**
     * @return int
     */
    public function getQuantityUpdated(): int
    {
        return $this->quantityUpdated;
    }

    /**
     * @param int $quantityUpdated
     */
    public function setQuantityUpdated(int $quantityUpdated): void
    {
        $this->quantityUpdated = $quantityUpdated;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getColor()
    {
        $color = '';
        if ($this->quantityUpdated === 0) {
            $color = 'red';
        } elseif ($this->state === self::STATE_UPDATED) {
            $color = 'orange';
        } elseif ($this->state === self::STATE_ADDED) {
            $color = 'blue';
        }
        return  $color;
    }
}
