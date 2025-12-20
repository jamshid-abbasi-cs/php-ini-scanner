<?php

declare(strict_types=1);

namespace Yousha\PhpIniScanner\Tests {

    use Throwable;
    use PHPUnit\Framework\TestCase;
    use Yousha\PhpIniScanner\Commands\ScanCommand;

    /**
     * @group Smoke
     */
    final class SmokeTest extends TestCase
    {
        private ScanCommand $scanCommand;

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
            $this->scanCommand = new ScanCommand();
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
            // Methods finalization codes.
            parent::tearDown();
        }

        /**
         * @test
         *
         * @small
         *
         * @return void
         */
        public function testComposerCanLoadLibraryClass(): void
        {
            // AAA
            $this->assertTrue(class_exists(ScanCommand::class));
        }

        /**
         * @test
         *
         * @small
         *
         * @return void
         */
        public function testLinterInstanceIsObject(): void
        {
            // AAA
            $this->assertNotNull($this->scanCommand);
            // AAA
            $this->assertIsObject($this->scanCommand);
        }
    }
}
