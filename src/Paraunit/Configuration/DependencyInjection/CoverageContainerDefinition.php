<?php

namespace Paraunit\Configuration\DependencyInjection;

use Paraunit\Configuration\PHPDbgBinFile;
use Paraunit\Configuration\PHPUnitBinFile;
use Paraunit\Configuration\PHPUnitConfig;
use Paraunit\Configuration\TempFilenameFactory;
use Paraunit\Coverage\CoverageFetcher;
use Paraunit\Coverage\CoverageMerger;
use Paraunit\Coverage\CoverageResult;
use Paraunit\Printer\CoveragePrinter;
use Paraunit\Process\CommandLineWithCoverage;
use Paraunit\Process\ProcessBuilderFactory;
use Paraunit\Proxy\XDebugProxy;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class CoverageContainerDefinition extends ParallelContainerDefinition
{
    public function configure(ContainerBuilder $container): ContainerBuilder
    {
        parent::configure($container);

        $this->configureCoverageConfiguration($container);
        $this->configureProcessWithCoverage($container);
        $this->configureCoverage($container);

        return $container;
    }

    private function configureCoverageConfiguration(ContainerBuilder $container)
    {
        $container->setDefinition(PHPDbgBinFile::class, new Definition(PHPDbgBinFile::class));
        $container->setDefinition(XDebugProxy::class, new Definition(XDebugProxy::class));
    }

    private function configureProcessWithCoverage(ContainerBuilder $container)
    {
        $container->setDefinition(CommandLineWithCoverage::class, new Definition(CommandLineWithCoverage::class, [
            new Reference(PHPUnitBinFile::class),
            new Reference(PHPDbgBinFile::class),
            new Reference(TempFilenameFactory::class),
        ]));
        $container->setDefinition(ProcessBuilderFactory::class, new Definition(ProcessBuilderFactory::class, [
            new Reference(CommandLineWithCoverage::class),
            new Reference(PHPUnitConfig::class),
            new Reference(TempFilenameFactory::class),
        ]));
    }

    private function configureCoverage(ContainerBuilder $container)
    {
        $container->setDefinition(CoverageFetcher::class, new Definition(CoverageFetcher::class, [
            new Reference(TempFilenameFactory::class),
            new Reference('paraunit.test_result.coverage_failure_container'),
        ]));
        $container->setDefinition(CoverageMerger::class, new Definition(CoverageMerger::class, [
            new Reference(CoverageFetcher::class),
        ]));
        $container->setDefinition(CoverageResult::class, new Definition(CoverageResult::class, [
            new Reference(CoverageMerger::class),
        ]));
        $container->setDefinition(CoveragePrinter::class, new Definition(CoveragePrinter::class, [
            new Reference(PHPDbgBinFile::class),
            new Reference(XDebugProxy::class),
            new Reference(OutputInterface::class),
        ]));
    }
}
