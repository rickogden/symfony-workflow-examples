<?php

declare(strict_types=1);

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class BeerGlass.
 *
 * @ODM\Document(collection="beer_glass")
 */
class BeerGlass
{
    /**
     * @var string
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string
     * @ODM\Field(type="string")
     */
    protected $state = 'clean';

    /**
     * @var array
     * @ODM\Field(type="hash")
     */
    protected $complexState = ['clean' => 1, 'empty' => 1];

    /**
     * @var int
     * @ODM\Field(type="int")
     */
    protected $full = 0;

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

    public function getComplexState(): array
    {
        return $this->complexState;
    }

    public function setComplexState(array $complexState): void
    {
        $this->complexState = $complexState;
    }

    /**
     * @return int
     */
    public function getFull(): int
    {
        return $this->full;
    }

    public function fill(int $fill = 100): void
    {
        if ($this->full + $fill > 100) {
            $this->full = 100;
        } else {
            $this->full += $fill;
        }
    }

    public function swig(int $amount = 10): void
    {
        if ($this->full - $amount <= 0) {
            $this->full = 0;
        } else {
            $this->full -= $amount;
        }
    }

    public function empty(): void
    {
        $this->full = 0;
    }
}
