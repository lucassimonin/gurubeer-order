<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 12/11/2018
 * Time: 18:45
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="gurubeer_order_version")
 * @ORM\Entity(repositoryClass="App\Repository\OrderVersionRepository")
 */
class OrderVersion
{
    public const STATE_DRAFT = 'draft';
    public const STATE_WAIT_RETURN = 'wait_return';
    public const STATE_RETURN_OK = 'return_ok';
    public const STATE_WAIT_CUSTOMER = 'wait_customer';
    public const STATE_READY = 'ready';
    public const STATE_FINISH = 'finish';
    public const STATE_NO_EDIT_QUANTITY = [
        'ready',
        'finish',
        'wait_palette'
    ];
    public const TRANSITION_WAIT_RETURN = 'to_wait_return';
    public const TRANSITION_CUSTOMER_RETURN = 'to_wait_customer_wait_return';
    public const TRANSITION_AVAILABLE = [
        'to_wait_return',
        'to_wait_customer',
        'to_return_ok',
        'to_return_wait_return',
        'to_return_ok_wait_customer',
        'to_ready',
        'to_finish',
        'to_wait_palette',
        'to_wait_customer_wait_return'
    ];
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $state = self::STATE_DRAFT;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Item", mappedBy="order", cascade={"persist", "remove"})
     * @var ArrayCollection
     */
    private $items;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="versions", fetch="EAGER")
     * @ORM\JoinColumn(nullable=true)
     * @var Order
     */
    private $order;

    /**
     * Creator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * @var User|null
     */
    protected $creator;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->version = 1;
    }

    public function __clone()
    {
        if ($this->id) {
            $this->setId(null);
            $itemsClone = new ArrayCollection();
            /** @var Item $item */
            foreach ($this->items as $item) {
                $itemClone = clone $item;
                $itemClone->setOrder($this);
                $itemsClone->add($itemClone);
            }
            $this->items = $itemsClone;
            $this->setVersion($this->version + 1);
        }
    }


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

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function clearItems(): void
    {
        $this->items->clear();
    }
    /**
     * {@inheritdoc}
     */
    public function countItems(): int
    {
        return $this->items->count();
    }
    /**
     * {@inheritdoc}
     */
    public function addItem(Item $item): void
    {
        if ($this->hasItem($item)) {
            return;
        }
        $this->items->add($item);
        $item->setOrder($this);
    }
    /**
     * {@inheritdoc}
     */
    public function removeItem(Item $item): void
    {
        if ($this->hasItem($item)) {
            $this->items->removeElement($item);
            $item->setOrder(null);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function hasItem(Item $item): bool
    {
        return $this->items->contains($item);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    /**
     * @return User|null
     */
    public function getCreator(): ?User
    {
        return $this->creator;
    }

    /**
     * @param User|null $creator
     */
    public function setCreator(?User $creator): void
    {
        $this->creator = $creator;
    }

    /**
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @param int $version
     */
    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @param ArrayCollection $items
     */
    public function setItems(ArrayCollection $items): void
    {
        $this->items = $items;
    }
}
