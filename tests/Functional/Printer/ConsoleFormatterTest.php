<?php
declare(strict_types=1);

namespace Tests\Functional\Printer;

use Paraunit\Printer\ConsoleFormatter;
use Paraunit\TestResult\TestResultContainer;
use Tests\BaseFunctionalTestCase;
use Symfony\Component\Console\Formatter\OutputFormatterStyleInterface;

/**
 * Class ConsoleFormatterTest
 * @package Tests\Functional\Printer
 */
class ConsoleFormatterTest extends BaseFunctionalTestCase
{
    /**
     * @dataProvider serviceTagsProvider
     */
    public function testOnEngineStartHasAllTagsRegistered(string $containerServiceName)
    {
        /** @var TestResultContainer $testResultContainer */
        $testResultContainer = $this->container->get($containerServiceName);
        /** @var ConsoleFormatter $consoleFormatter */
        $consoleFormatter = $this->container->get('paraunit.printer.console_formatter');
        $outputInterface = $this->container->get('output');

        $consoleFormatter->onEngineBeforeStart();

        $tag = $testResultContainer->getTestResultFormat()->getTag();
        $formatter = $outputInterface->getFormatter();
        $style = $formatter->getStyle($tag);
        $this->assertInstanceOf(
            OutputFormatterStyleInterface::class,
            $style,
            'Missing tag style: ' . $tag . ' -- service ' . $containerServiceName
        );
    }

    /**
     * @return string[]
     */
    public function serviceTagsProvider(): array
    {
        return [
            ['paraunit.test_result.abnormal_terminated_container'],
            ['paraunit.test_result.error_container'],
            ['paraunit.test_result.failure_container'],
            ['paraunit.test_result.warning_container'],
            ['paraunit.test_result.risky_container'],
            ['paraunit.test_result.skipped_container'],
            ['paraunit.test_result.incomplete_container'],
        ];
    }
}
