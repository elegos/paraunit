<?php
declare(strict_types=1);

namespace Tests\Functional\Parser\JSON;

use Paraunit\Lifecycle\ProcessEvent;
use Paraunit\Parser\JSON\LogParser;
use Paraunit\TestResult\Interfaces\PrintableTestResultInterface;
use Tests\BaseFunctionalTestCase;
use Tests\Stub\PHPUnitJSONLogOutput\JSONLogStub;
use Tests\Stub\StubbedParaunitProcess;

/**
 * Class LogParserTest
 * @package Tests\Functional\Parser
 */
class LogParserTest extends BaseFunctionalTestCase
{
    /**
     * @dataProvider parsableResultsProvider
     */
    public function testParse(string $stubLog, string $expectedResult, bool $hasAbnormalTermination = false)
    {
        $process = new StubbedParaunitProcess();
        $this->createLogForProcessFromStubbedLog($process, $stubLog);

        /** @var LogParser $parser */
        $parser = $this->container->get(LogParser::class);

        $parser->onProcessTerminated(new ProcessEvent($process));

        $results = $process->getTestResults();
        $this->assertContainsOnlyInstancesOf(PrintableTestResultInterface::class, $results);
        $textResults = '';
        /** @var PrintableTestResultInterface $singleResult */
        foreach ($results as $singleResult) {
            $textResults .= $singleResult->getTestResultFormat()->getTestResultSymbol();
        }
        $this->assertEquals($expectedResult, $textResults);

        $this->assertEquals($hasAbnormalTermination, $process->hasAbnormalTermination());

        if ($process->getTestClassName()) {
            $this->assertStringStartsWith('Paraunit\Tests\Stub\\', $process->getTestClassName());
        }
    }

    /**
     * @return string[]
     */
    public function parsableResultsProvider(): array
    {
        return [
            [JSONLogStub::TWO_ERRORS_TWO_FAILURES, 'FF..E...E'],
            [JSONLogStub::ALL_GREEN, '.........'],
            [JSONLogStub::ONE_ERROR, '.E.'],
            [JSONLogStub::ONE_INCOMPLETE, '..I.'],
            [JSONLogStub::ONE_RISKY, '..R.'],
            [JSONLogStub::ONE_SKIP, '..S.'],
            [JSONLogStub::ONE_WARNING, '...W'],
            [JSONLogStub::FATAL_ERROR, '...X', true],
            [JSONLogStub::SEGFAULT, '...X', true],
            [JSONLogStub::PARSE_ERROR, '...................................................X', true],
            [JSONLogStub::UNKNOWN, '?', false],
        ];
    }

    public function testParseHandlesMissingLogsAsAbnormalTerminations()
    {
        /** @var LogParser $parser */
        $parser = $this->container->get(LogParser::class);
        $process = new StubbedParaunitProcess();
        $process->setExitCode(139);

        $parser->onProcessTerminated(new ProcessEvent($process));

        $results = $process->getTestResults();
        $this->assertContainsOnlyInstancesOf(PrintableTestResultInterface::class, $results);
        $this->assertCount(1, $results);

        $this->assertEquals('X', $results[0]->getTestResultFormat()->getTestResultSymbol());
        $this->assertTrue($process->hasAbnormalTermination());
    }
}
