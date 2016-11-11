<?php

namespace Paraunit\Process;

use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;

/**
 * Class TestCliCommand
 * @package Paraunit\Process
 */
class TestCommandLine implements CliCommandInterface
{
    /** @var  PHPUnitBinFile */
    private $phpUnitBin;

    /** @var  TempFilenameFactory */
    protected $filenameFactory;

    /**
     * TestCliCommand constructor.
     * @param PHPUnitBinFile $phpUnitBin
     * @param TempFilenameFactory $filenameFactory
     */
    public function __construct(PHPUnitBinFile $phpUnitBin, TempFilenameFactory $filenameFactory)
    {
        $this->phpUnitBin = $phpUnitBin;
        $this->filenameFactory = $filenameFactory;
    }

    /**
     * @return string
     */
    public function getExecutable()
    {
        return $this->phpUnitBin->getPhpUnitBin();
    }

    /**
     * @param PHPUnitConfig $configFile
     * @param string $uniqueId
     * @return string
     */
    public function getOptions(PHPUnitConfig $configFile, $uniqueId)
    {
        return '-c ' . $configFile->getFileFullPath()
            . ' --log-json ' . $this->filenameFactory->getFilenameForLog($uniqueId);
    }
}
