<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Ports;

use Mashbo\CoreRepository\Domain\CQRS\Command\CommandBusInterface;
use Mashbo\CoreRepository\Domain\CQRS\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Service\Attribute\Required;

abstract class CQRSController extends AbstractController
{
    protected QueryBusInterface $queryBus;
    protected CommandBusInterface $commandBus;

    #[Required]
    public function setQueryBus(QueryBusInterface $queryBus): void
    {
        $this->queryBus = $queryBus;
    }

    #[Required]
    public function setCommandBus(CommandBusInterface $commandBus): void
    {
        $this->commandBus = $commandBus;
    }
}
