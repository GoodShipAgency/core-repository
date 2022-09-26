<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Model;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    public function getId(): int
    {
        if ($this->id === null) {
            throw new \LogicException(sprintf('This %s entity does not yet have an ID', __CLASS__));
        }

        return $this->id;
    }
}
