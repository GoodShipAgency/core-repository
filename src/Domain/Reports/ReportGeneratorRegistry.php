<?php

declare(strict_types=1);

namespace Mashbo\CoreRepository\Domain\Reports;

class ReportGeneratorRegistry
{
    /**
     * @var array<string, ReportGenerator>
     */
    private array $generators;

    /**
     * @param iterable<array-key, ReportGenerator> $generators
     */
    public function __construct(iterable $generators)
    {
        $this->generators = [];
        foreach ($generators as $generator) {
            $this->generators[$generator::class] = $generator;
        }
    }

    public function find(string $type): ReportGenerator
    {
        if (array_key_exists($type, $this->generators)) {
            return $this->generators[$type];
        }

        throw new \LogicException("Report generator of type $type not found");
    }
}
