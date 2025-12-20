<?php

declare(strict_types=1);

namespace Yousha\PhpIniScanner\Tests\Commands;

use Yousha\PhpIniScanner\Commands\ScanCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

final class ScanCommandTest extends TestCase
{
    private string $tempIni;

    /**
     * This method is called before the first test method in the test class is executed.
     *
     * @doesNotPerformAssertions
     *
     * @return void
     */
    public static function setUpBeforeClass(): void {}

    /**
     * This method is called after the last test method in the test class has been executed.
     *
     * @doesNotPerformAssertions
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        gc_collect_cycles();
    }

    /**
     * This method is called BEFORE each test method.
     *
     * @doesNotPerformAssertions
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->tempIni = tempnam(sys_get_temp_dir(), 'test_cli_');
        file_put_contents($this->tempIni, "display_errors = Off\nlog_errors = On");
    }

    /**
     * This method is called AFTER each test method.
     *
     * @doesNotPerformAssertions
     *
     * @return void
     */
    protected function tearDown(): void
    {
        if (file_exists($this->tempIni)) {
            unlink($this->tempIni);
        }

        parent::tearDown();
    }

    /**
     * @test
     *
     * @small
     *
     * @return void
     */
    public function testCommandFailsWithoutMode(): void
    {
        $application = new Application();
        $application->add(new ScanCommand());
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'scan',
            '-i' => $this->tempIni,
        ]);
        $output = new BufferedOutput();
        $statusCode = $application->run($input, $output);
        $content = $output->fetch();
        $this->assertEquals(1, $statusCode);
        $this->assertStringContainsString('Error: You must specify a mode', $content);
    }

    /**
     * @test
     *
     * @small
     *
     * @return void
     */
    public function testCommandSuccessfulExecution(): void
    {
        $application = new Application();
        $application->add(new ScanCommand());
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => 'scan',
            '-p' => true,
            '-i' => $this->tempIni,
        ]);
        $output = new BufferedOutput();
        $application->run($input, $output);
        $content = $output->fetch();
        $this->assertStringContainsString('Scan result of php.ini for', $content);
        $this->assertStringContainsString('display_errors', $content);
    }
}
