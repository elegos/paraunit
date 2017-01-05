<?php

namespace Paraunit\Configuration;

use Symfony\Component\Process\Process;

/**
 * Class PHPDbgBinFile
 * @package Paraunit\Configuration
 */
class PHPDbgBinFile
{
    /** @var  string Realpath to the PHPDbg bin location */
    private $phpDbgBin;

    /**
     * PHPDbgBinFile constructor.
     */
    public function __construct()
    {
        $this->phpDbgBin = $this->getPhpDbgBinLocation();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getPhpDbgBin()
    {
        if (! $this->isAvailable()) {
            throw new \RuntimeException('PHPDbg is not available!');
        }

        return $this->phpDbgBin;
    }

    public function isAvailable()
    {
        return $this->phpDbgBin !== '';
    }

    /**
     * @return string
     */
    private function getPhpDbgBinLocation()
    {
        $locator = new Process('command -v phpdbg');
        $locator->run();

        return (string) preg_replace('/\s/', '', $locator->getOutput());
    }
}