<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Application\CQRS;

use Mashbo\CoreRepository\Domain\Model\AggregateId;

interface AggregateAwareCommand
{
    public function getResourceAggregateId(): AggregateId;
}
