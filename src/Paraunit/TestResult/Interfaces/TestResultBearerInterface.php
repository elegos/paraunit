<?php

declare(strict_types=1);

namespace Paraunit\TestResult\Interfaces;

/**
 * Interface TestResultBearerInterface
 * @package Paraunit\TestResult\Interfaces
 */
interface TestResultBearerInterface
{
    /**
     * @return PrintableTestResultInterface[]
     */
    public function getTestResults(): array;
}
