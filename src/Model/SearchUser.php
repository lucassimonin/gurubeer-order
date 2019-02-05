<?php

/**
 * Model for search
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Model;

/**
 * Class SearchUser
 *
 * @package App\Model\User
 */
class SearchUser implements SearchInterface
{
    public static $limit = 20;

    /**
     * @var string|null
     */
    protected $email;

    /** @var int  */
    private $page = 1;

    public function __construct(array $queries)
    {
        $this->page = $queries['page'] ?? 1;
        $this->email = $queries['email'] ?? null;
    }

    public function getFilters(): array
    {
        return [
            'email' => $this->email
        ];
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }
}
