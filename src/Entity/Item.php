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

    public const LIST_TYPE = [
        self::TYPE_BARREL => 'admin.type.'.self::TYPE_BARREL,
        self::TYPE_BOTTLE => 'admin.type.'.self::TYPE_BOTTLE
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
    private $type = self::TYPE_BARREL;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $quantity = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $quantityUpdated = 0;


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

    public function getColor()
    {
        $color = '';
        if ($this->quantityUpdated === 0) {
            $color = 'red';
        } elseif ($this->quantityUpdated !== $this->quantity) {
            $color = 'orange';
        }
        return  $color;
    }
}
