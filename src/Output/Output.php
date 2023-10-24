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
abstract class Output implements OutputInterface
{

    // protected Colors $colors;

    public function __construct()
    {
        // $this->colors = new Colors();
    }


    /**
     * writes message content on terminal
     */
    public function out($message)
    {
        echo $message;
    }

    /**
     * Creates a newline on the terminal
     */
    public function newline()
    {
        $this->out("\n");
    }
    /**
     * Creates a gap between text on the terminal
     */
    public function newGap()
    {
        $this->out("\t");
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     */
    public function writeln(string $message)
    {
        $this->out($this->format($message));
        $this->newline();
    }

    /**
     * Writes status messages to the output 
     */
    public function error(string $message)
    {
        $this->newline();
        $message = $this->format($message, Colors::$C_white, Colors::$Bg_red);
        $this->out($message);
        $this->newline();
        $this->newline();
    }
    public function success(string $message)
    {
        $this->newline();
        $message = $this->format($message, Colors::$C_black, Colors::$Bg_green);
        $this->out($message);
        $this->newline();
        $this->newline();
    }
    public function warning(string $message)
    {
        $this->newline();
        $message = $this->format($message, Colors::$C_black, Colors::$Bg_yellow);
        $this->out($message);
        $this->newline();
        $this->newline();
    }

    /**
     * Writes Colored messages to the output 
     */
    public function writelnError(string $message)
    {
        $message = $this->format($message, Colors::$C_red);
        $this->out($message);
        // $this->newline();
    }
    public function writelnInfo(string $message)
    {
        $message = $this->format($message, Colors::$C_green);
        $this->out($message);
        // $this->newline();
    }
    public function writelnWarning(string $message)
    {
        $message = $this->format($message, Colors::$C_yellow);
        $this->out($message);
        // $this->newline();
    }

    protected function format($text, $color = false, $background = false): string
    {
        $setup = '';

        if ($color && $background) {
            $setup .= $color;
            $setup .= ";" . $background;
        } elseif ($color) {
            $setup .= $color;
        } elseif ($background) {
            $setup .= $background;
        }
        $setup .= "m";

        $str = "\033[$setup $text\033[0m";
        return $str;
    }
}
