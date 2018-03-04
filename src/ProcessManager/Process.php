<?php

namespace ProcessManager;

class Process
{
    const STD_IN_PIPE = 0;
    const STD_OUT_PIPE = 1;
    const STD_ERR_PIPE = 2;

    /** @var resource */
    private $process;

    /** @var resource */
    private $stdIn = null;

    /** @var resource */
    private $stdOut = null;

    /** @var resource */
    private $stdErr = null;

    /**
     * @param resource $process
     * @param array $pipes
     */
    public function __construct($process, $pipes)
    {
        $this->process = $process;

        if (array_key_exists(self::STD_IN_PIPE, $pipes)) {
            $this->stdIn = $pipes[self::STD_IN_PIPE];
        }

        if (array_key_exists(self::STD_OUT_PIPE, $pipes)) {
            $this->stdOut = $pipes[self::STD_OUT_PIPE];
        }

        if (array_key_exists(self::STD_ERR_PIPE, $pipes)) {
            $this->stdErr = $pipes[self::STD_ERR_PIPE];
        }
    }

    public function __destruct()
    {
        $this->close();
    }


    /** @return int process exit code */
    public function close() {
        $this->closeStreams();

        if (is_resource($this->process)) {
            proc_terminate($this->process);
        }
        return proc_close($this->process);
    }

    public function detach() {
        $this->closeStreams();
        proc_close($this->process);
    }

    /**
     * @return string
     * @throws ProcessException
     */
    public function readOutput()
    {
        if ($this->stdOut === null) {
            throw new ProcessException("StdOut is not intercepted. Cannot read!");
        }

        $str = "";
        while (!feof($this->stdOut))
        {
            $str .= fread($this->stdOut, 10);
            echo feof($this->stdOut);
        }
        return $str;
    }

    /**
     * @param string $input
     *
     * @return Process
     * @throws ProcessException
     */
    public function writeToProcess($input)
    {
        if ($this->stdIn === null) {
            throw new ProcessException("StdIn is not intercepted. Cannot write!");
        }

        $bytesWriten = fwrite($this->stdIn, $input);
        if (strlen($input) === false || strlen($input) !== $bytesWriten) {
            throw new ProcessException("Error during write to StdIn.");
        }

        return $this;
    }

    public function closeStdIn()
    {
        if ($this->stdIn !== null) {
            fclose($this->stdIn);
        }
    }

    public function closeStreams()
    {
        if (is_resource($this->stdIn)) {
            fclose($this->stdIn);
        }
        if (is_resource($this->stdOut)) {
            fclose($this->stdOut);
        }
        if (is_resource($this->stdErr)) {
            fclose($this->stdErr);
        }
    }
}