<?php

/**
 * Model for search
 *
 * @author Lucas Simonin <lsimonin2@gmail.com>
 */

namespace App\Form\Model;

/**
 * Class SearchOrder
 *
 * @package App\Form\Model
 */
class SearchOrder
{


    /**
     * @var string|null
     */
    protected $name;

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
     * Get search data
     *
     * @return array
     */
    public function getSearchData()
    {
        $tab = [];
        if (!empty($this->name)) {
            $tab['name'] = $this->name;
        }

        return $tab;
    }
}
