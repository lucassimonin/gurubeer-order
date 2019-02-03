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
     * @ORM\OneToMany(targetEntity="App\Entity\OrderVersion", mappedBy="order", cascade={"persist", "remove"})
     * @var ArrayCollection
     */
    private $versions;

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
     * Creator
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     * @var User|null
     */
    private $creator;

    /**
     * Order constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->versions = new ArrayCollection();
        $this->created = new \DateTime();
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
     * @return ArrayCollection
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * {@inheritdoc}
     */
    public function clearVersions(): void
    {
        $this->versions->clear();
    }
    /**
     * {@inheritdoc}
     */
    public function countVersions(): int
    {
        return $this->versions->count();
    }
    /**
     * {@inheritdoc}
     */
    public function addVersion(OrderVersion $version): void
    {
        if ($this->hasVersion($version)) {
            return;
        }
        $this->versions->add($version);
        $version->setOrder($this);
    }
    /**
     * {@inheritdoc}
     */
    public function removeVersion(OrderVersion $version): void
    {
        if ($this->hasVersion($version)) {
            $this->versions->removeElement($version);
            $version->setOrder(null);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function hasVersion(OrderVersion $version): bool
    {
        return $this->versions->contains($version);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->versions->isEmpty();
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
    public function setPdf($pdf): void
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
}
