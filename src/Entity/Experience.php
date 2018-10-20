<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContentRepository")
 * @ORM\Table(name="experience")
 */
class Experience
{
    use ORMBehaviors\Translatable\Translatable;
    const EXPERIENCE_TYPE_EDUCATION = 'education';
    const EXPERIENCE_TYPE_WORK = 'work';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="admin.validation.mandatory.label")
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(name="city_url", type="string", length=255)
     * @Assert\NotBlank(message="admin.validation.mandatory.label")
     * @Assert\Url(message="admin.validation.mandatory.label")
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(length=255, nullable=true)
     * @Assert\NotBlank(message="admin.validation.mandatory.label")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(length=255, nullable=true)
     * @Assert\NotBlank(message="admin.validation.mandatory.label")
     */
    private $duration;

    /**
     *
     * @var \DateTime
     * @ORM\Column(name="start_date", type="date")
     */
    private $startDate;

    /**
     * Experience constructor.
     */
    public function __construct()
    {
        $this->type = self::EXPERIENCE_TYPE_WORK;
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
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
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
     * @return string
     */
    public function getDuration(): ?string
    {
        return $this->duration;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @param string $duration
     */
    public function setDuration(string $duration): void
    {
        $this->duration = $duration;
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (!method_exists(self::getTranslationEntityClass(), $method)) {
            $method = 'get'.ucfirst($method);
        }

        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->__call('name', array());
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->__call('name', array());
    }
}