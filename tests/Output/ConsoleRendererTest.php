<?php

declare(strict_types=1);

namespace Yousha\PhpIniScanner\Tests\Output;

use Yousha\PhpIniScanner\Output\ConsoleRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

final class ConsoleRendererTest extends TestCase
{
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
        // ...
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
        // ...
        parent::tearDown();
    }

    /**
     * @test
     *
     * @small
     *
     * @return void
     */
    public function testRendererDrawsTableWithCorrectData(): void
    {
        $output = new BufferedOutput();
        $renderer = new ConsoleRenderer();
        $results = [
            [
                'setting' => 'memory_limit',
                'expected' => '128M',
                'current' => '128M',
                'pass' => true,
            ],
        ];
        $renderer->render($output, $results, 'Production');
        $content = $output->fetch();
        // Check for table structure elements.
        $this->assertStringContainsString('memory_limit', $content);
        $this->assertStringContainsString('128M', $content);
        $this->assertStringContainsString('PASS', $content);
        $this->assertStringContainsString('Checked: 1', $content);
    }
}
