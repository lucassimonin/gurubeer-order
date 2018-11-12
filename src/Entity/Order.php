<?php
/**
 * Created by PhpStorm.
 * User: lucas
 * Date: 12/11/2018
 * Time: 18:45
 */

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="gurubeer_order")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Order
{
    use TimestampableTrait;
    public const STATE_DRAFT = 'draft';
    public const STATE_IN_PROGRESS = 'in_progress';
    public const STATE_PREPARATION = 'preparation';
    public const STATE_READY = 'ready';
    public const STATE_FINISH = 'finish';
    public const TRANSITION_IN_PROGRESS = 'to_in_progress';
    public const TRANSITION_AVAILABLE = ['to_in_progress', 'to_preparation', 'to_ready', 'to_finish'];
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
     * @var string|null
     */
    private $itemsText;

    /**
     * Order constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
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
    public function getItems(): ArrayCollection
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
     * @return null|string
     */
    public function getItemsText(): ?string
    {
        return $this->itemsText;
    }

    /**
     * @param null|string $itemsText
     */
    public function setItemsText(?string $itemsText): void
    {
        $this->itemsText = $itemsText;
    }
}
