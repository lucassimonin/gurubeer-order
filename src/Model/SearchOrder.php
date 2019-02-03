<?php

/**
 * Model for search
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Model;

/**
 * Class SearchOrder
 *
 * @package App\Form\Model
 */
class SearchOrder implements SearchInterface
{
    public static $limit = 20;

    /**
     * @var string|null
     */
    protected $name;

    /** @var int  */
    private $page = 1;

    public function __construct(array $queries)
    {
        $this->page = $queries['page'] ?? 1;
        $this->name = $queries['name'] ?? null;
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

    public function getFilters(): array
    {
        return [
          'name' => $this->name
        ];
    }

    public function getPage(): int
    {
        return $this->page;
    }
}
