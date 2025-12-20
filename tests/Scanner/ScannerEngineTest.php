<?php

declare(strict_types=1);

namespace Yousha\PhpIniScanner\Tests\Scanner;

use Yousha\PhpIniScanner\Scanner\ScannerEngine;
use Yousha\PhpIniScanner\Config\ProductionRules;
use PHPUnit\Framework\TestCase;

final class ScannerEngineTest extends TestCase
{
    private string $tempIni;

    private ScannerEngine $engine;

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
        $this->engine = new ScannerEngine();
        $this->tempIni = tempnam(sys_get_temp_dir(), 'test_ini_');
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
    public function testScannerIdentifiesPassingRules(): void
    {
        // Simulate a php.ini that perfectly matches Production.
        $content = "display_errors = Off\n";
        $content .= "log_errors = On\n";
        $content .= "expose_php = 0\n";
        file_put_contents($this->tempIni, $content);
        $rules = new ProductionRules();
        $results = $this->engine->scanFile($this->tempIni, $rules);

        // Find specific display_errors result.
        foreach ($results as $res) {
            if ($res['setting'] === 'display_errors') {
                $this->assertTrue($res['pass'], 'display_errors=Off should pass ProductionRules');
                $this->assertEquals('0', $res['current']);
            }
        }
    }

    /**
     * @test
     *
     * @small
     *
     * @return void
     */
    public function testScannerIdentifiesFailingRules(): void
    {
        // Secure production file should have display_errors = 0.
        // We set it to 1 to force a failure.
        file_put_contents($this->tempIni, "display_errors = On");
        $rules = new ProductionRules();
        $results = $this->engine->scanFile($this->tempIni, $rules);

        foreach ($results as $res) {
            if ($res['setting'] === 'display_errors') {
                $this->assertFalse($res['pass'], 'display_errors=On should fail ProductionRules');
                $this->assertEquals('1', $res['current']);
            }
        }
    }

    /**
     * @test
     *
     * @small
     *
     * @return void
     */
    public function testNormalizationLogic(): void
    {
        // PHP ini often uses 'Off', 'On', or empty strings.
        file_put_contents($this->tempIni, "html_errors = Off\nallow_url_fopen = On");
        $results = $this->engine->scanFile($this->tempIni, new ProductionRules());

        foreach ($results as $res) {
            if ($res['setting'] === 'html_errors') {
                $this->assertEquals('0', $res['current']);
            }

            if ($res['setting'] === 'allow_url_fopen') {
                $this->assertEquals('1', $res['current']);
            }
        }
    }

    /**
     * @test
     *
     * @small
     *
     * @return void
     */
    public function testThrowsExceptionOnMissingFile(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->engine->scanFile('/non/existent/path/php.ini', new ProductionRules());
    }
}
