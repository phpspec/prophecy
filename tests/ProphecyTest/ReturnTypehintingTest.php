<?php
namespace ProphecyTest;

class ReturnTypehinting extends \PHPUnit_Framework_TestCase
{
    public function testSpiesReturnTypehintingCompatibility()
    {
        if (version_compare(PHP_VERSION, '7.0.0') < 0) {
            $this->markTestSkipped('Functionality not available in < PHP 7.0.0');
        }

        $output = array();
        chdir(__DIR__ . '/../examples/');
        exec('../../vendor/bin/phpspec r', $output);

        $this->assertFalse(
            $this->thereIsAFatalError($output),
            'Fatal error occurred when trying to run test with Prophecy spies and PHP 7 return typehinting'
        );
    }

    private function thereIsAFatalError(array $consoleOutput)
    {
        foreach ($consoleOutput as $consoleOutputLine) {
            if (strpos($consoleOutputLine, 'Fatal error happened') !== false) {
                return true;
            }
        }
        return false;
    }
}
