<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ?int $id = null;

    public function getId(): int
    {
        if ($this->id === null) {
            throw new \LogicException(sprintf('This %s entity does not yet have an ID', __CLASS__));
        }
        return $this->id;
    }
}
