<?php
namespace ProcessManager;

class ProcessManager
{
    /**
     * @param string   $cmd
     * @param string[] $params
     *
     * @return ProcessExecutor
     */
    public static function executor($cmd)
    {
        return new ProcessExecutor($cmd);
    }
}