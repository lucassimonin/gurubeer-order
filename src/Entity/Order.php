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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="gurubeer_order")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Order
{
    use TimestampableTrait;
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
     * @var string
     * @ORM\Column(type="string")
     */
    private $beforeState = self::STATE_DRAFT;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Item", mappedBy="order", cascade={"persist", "remove"})
     * @var ArrayCollection
     */
    private $items;

    /**
     * Creator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * @var User|null
     */
    protected $creator;

    /**
     * @var string|null
     */
    private $itemsText;

    /**
     *
     * @Assert\File(mimeTypes={ "application/pdf" })
     * @var UploadedFile|File
     */
    private $pdf;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileName;

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
     * @return string
     */
    public function getBeforeState(): string
    {
        return $this->beforeState;
    }

    /**
     * @param string $beforeState
     */
    public function setBeforeState(string $beforeState): void
    {
        $this->beforeState = $beforeState;
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

    /**
     * @return mixed
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * @param $pdf
     */
    public function setPdf( $pdf): void
    {
        $this->pdf = $pdf;
    }

    /**
     * @return string
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     */
    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }
}
