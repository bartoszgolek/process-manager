<?php
namespace ProcessManager\Test;

use ProcessManager\ProcessManager;

class ProcessManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @throws \ProcessManager\ProcessExecutionException
     * @throws \ProcessManager\ProcessException
     */
    public function testExecutor_WillHandleStdIn()
    {
        $input = "input string";

        $process = ProcessManager::executor("cat")
            ->interceptStdIn()
            ->interceptStdOut()
            ->execute();

        $process->writeToProcess($input)->closeStdIn();

        $output = $process->readOutput();
        $process->close();

        self::assertEquals($input, $output);
    }

    /**
     * @throws \ProcessManager\ProcessExecutionException
     * @throws \ProcessManager\ProcessException
     */
    public function testExecutor_WillHandleParameters()
    {
        $process = ProcessManager::executor("echo")
            ->withParameters("1", "2", "3")
            ->interceptStdOut()
            ->execute();

        $output = $process->readOutput();
        $process->close();

        self::assertEquals("1 2 3" . PHP_EOL, $output);
    }

    /**
     * @throws \ProcessManager\ProcessExecutionException
     * @throws \ProcessManager\ProcessException
     */
    public function testExecutor_WillHandleParametersAdded()
    {
        $process = ProcessManager::executor("echo")
            ->withParameter("1")
            ->withParameter("2")
            ->interceptStdOut()
            ->execute();

        $output = $process->readOutput();
        $process->close();

        self::assertEquals("1 2" . PHP_EOL, $output);
    }
}
