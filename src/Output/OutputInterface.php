<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */


namespace Devlee\WakerCLI\Output;

/**
 * 
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-CLI
 *
 */
interface OutputInterface
{
    /**
     * Helper Functions
     */
    public function out($message);
    public function newline();
    public function newGap();

    /**
     * Writes a message to the output and adds a newline at the end.
     */
    public function writeln(string $messages);

    /**
     * Writes status messages to the output 
     */
    public function error(string $message);
    public function success(string $message);
    public function warning(string $message);

    /**
     * Writes Colored messages to the output 
     */
    public function writelnError(string $message);
    public function writelnInfo(string $message);
    public function writelnWarning(string $message);
}
