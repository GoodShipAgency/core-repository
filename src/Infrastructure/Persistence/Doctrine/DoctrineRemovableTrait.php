<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Infrastructure\Persistence\Doctrine;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @template T of object
 */
trait DoctrineRemovableTrait
{
    abstract protected function getManager(): EntityManagerInterface;

    public function remove(object $record): void
    {
        $this->getManager()->remove($record);
        $this->getManager()->flush();
    }
}
