<?php
namespace ProcessManager;

class ProcessExecutor
{
    /** @var string|null */
    private $workingDirectory = null;

    /** @var string|null */
    private $cmd = null;

    /** @var array */
    private $params = [];

    /** @var array|null */
    private $stdIn = null;

    /** @var array|null */
    private $stdOut = null;

    /** @var array|null */
    private $stdErr = null;

    /** @var array|null */
    private $env = null;

    /**
     * @param string $cmd
     * @param array $params
     */
    public function __construct($cmd)
    {
        $this->cmd = $cmd;
    }

    /**
     * @return Process $process
     * @throws ProcessExecutionException
     */
    public function execute()
    {
        $process = proc_open(
            $this->getCommand(),
            $this->getDescriptorSpec(),
            $pipes,
            $this->getWorkingDirectory(),
            $this->env);

        if (!is_resource($process)) {
            throw new ProcessExecutionException();
        }

        return new Process($process, $pipes);
    }

    /**
     * @param string $env
     * @param string $value
     *
     * @return ProcessExecutor
     */
    public function addEnv($env, $value)
    {
        if ($this->env === null) {
            $this->env = [$env => $value];
        } else {
            $this->env[$env] = $value;
        }

        return $this;
    }

    public function interceptStdIn()
    {
        $this->stdIn = ["pipe", "r"];
        return $this;
    }

    public function interceptStdOut()
    {
        $this->stdOut = ["pipe", "w"];
        return $this;
    }

    public function interceptStdErr()
    {
        $this->stdOut = ["pipe", "r"];
        return $this;
    }

    /** @return null|string */
    private function getWorkingDirectory()
    {
        if ($this->workingDirectory != null) {
            return realpath($this->workingDirectory);
        }

        return $this->workingDirectory;
    }

    /**
     * @return array
     */
    private function getDescriptorSpec()
    {
        $descriptorSpec = [];
        if (!empty($this->stdIn)) {
            $descriptorSpec[0] = $this->stdIn;
        }
        if (!empty($this->stdOut)) {
            $descriptorSpec[1] = $this->stdOut;
        }
        if (!empty($this->stdErr)) {
            $descriptorSpec[0] = $this->stdErr;
        }
        return $descriptorSpec;
    }

    /**
     * @return null|string
     */
    private function getCommand()
    {
        if (empty($this->params)) {
            return $this->cmd;
        }

        return $this->cmd . " " . implode(" ", $this->params);
    }

    /**
     * @param string $parameters
     *
     * @return ProcessExecutor
     */
    public function withParameters($parameters)
    {
        $parameters = func_get_args();
        $this->params += $parameters;

        return $this;
    }

    /**
     * @param string $parameter
     * @return $this
     */
    public function withParameter($parameter)
    {
        $this->params[] = $parameter;

        return $this;
    }
}