<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class BeerGlass
 * @package App\Document
 * @ODM\Document(collection="beer_glass")
 */
class BeerGlass
{
    /**
     * @var string
     * @ODM\Id()
     */
    protected $id;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $state = 'clean';

    /**
     * @return mixed
     */
    public function getId(): string
    {
        return $this->id;
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


}