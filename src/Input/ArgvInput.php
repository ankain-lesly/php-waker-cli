<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */


namespace Devlee\WakerCLI\Input;

use Devlee\WakerCLI\Exception\ConsoleException;

/**
 * 
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-CLI
 *
 */

class ArgvInput extends Input
{
    private array $tokens;
    private array $globalOptions;
    // private array $parsed;

    public function __construct(array $argv = null, $definition = null)
    {
        $argv ??= $_SERVER['argv'] ?? [];

        // strip the application name
        array_shift($argv);

        $this->tokens = $argv;
        $this->globalOptions = ['help', 'name', 'version'];

        parent::__construct($definition);
    }

    protected function parse()
    {
        $parseOptions = true;
        while (null !== $token = \array_shift($this->tokens)) {
            if ($parseOptions === true) {
                $parseOptions = false;
                continue;
            }
            $this->parseToken($token);
        }
    }

    /**
     * Extract and validate cli arguments and options
     */
    protected function parseToken(string $token)
    {
        if (str_starts_with($token, '--')) {
            $this->parseLongOption($token);
        } elseif (str_starts_with($token, '-')) {
            $this->parseShortOption($token);
        } else {
            $this->parseArgument($token);
        }
    }

    /**
     * Parses a short option.
     */
    private function parseShortOption(string $token): void
    {
        if (\str_contains($token, '=')) {
            $token = \explode('=', $token);
        } else {
            $sub[] = \substr($token, 0, 2);
            $sub[] = \substr($token, 2);
            $token = $sub;
        }
        $name = \str_replace('-', '', $token[0]);
        $value = $token[1] ?? false;
        $option = $this->definitionGetOptFlag($name);

        if ($option) {
            if ($option['mode'] === Input::OPTION_VALUE_NONE) {
                if ($value) {
                    throw new ConsoleException(sprintf('Option "-%s" does not require a value', $name));
                }
            } elseif ($option['mode'] === Input::OPTION_VALUE_REQUIRED) {
                if (!$value) {
                    throw new ConsoleException(sprintf('Option "-%s" requires a valid value', $name));
                }
            }
            $this->options[$option['_name']] = $value ? $value : true;
        } else {
            $message = \sprintf('Un expected option, "%s" expected "%s".', $name, implode(', ', array_map(fn ($flag) => "--" . $flag, array_keys($this->definitionGetOpts()))));
            throw new ConsoleException($message);
        }
    }

    /**
     * Parses a long option.
     */
    private function parseLongOption(string $token): void
    {
        $token = \explode('=', $token);
        $name = \str_replace('--', '', $token[0]);
        $value = $token[1] ?? false;
        $options = $this->definitionGetOpts();

        // if ()) return;

        if ($option = ($options[$name] ?? false) || in_array($name, $this->globalOptions)) {
            if (($option['mode'] ?? false) === Input::OPTION_VALUE_NONE) {
                if ($value) {
                    throw new ConsoleException(sprintf('Option "--%s" does not require a value', $name));
                }
            } elseif (($option['mode'] ?? false) === Input::OPTION_VALUE_REQUIRED) {
                if (!$value) {
                    throw new ConsoleException(sprintf('Option "--%s" requires a valid value', $name));
                }
            }
            $this->options[$name] = $value ? $value : true;
        } else {
            $message = \sprintf('Un expected option, "%s" expected "%s".', $name, implode(', ', array_map(fn ($flag) => "--" . $flag, array_keys($this->definitionGetOpts()))));
            throw new ConsoleException($message);
        }
    }

    /**
     * Parses an argument.
     */
    private function parseArgument(string $token): void
    {
        $args = \array_keys($this->definitionGetArgs());
        $countDefArgs = \count($args);
        $countArgs = \count($this->arguments);

        if ($countArgs < $countDefArgs && $countDefArgs  > 0) {
            $this->arguments[$args[$countArgs]] = $token;
        } else {
            if ($countDefArgs <= 0) {
                $message = \sprintf('No arguments expected, got "%s".', $token);
            } else {
                $message = \sprintf('Too many arguments, expected arguments "%s".', implode('" "', $args));
            }
            throw new ConsoleException($message);
        }
    }

    /**
     * Get first argument in list.
     */
    public function getFirstArgument(): ?string
    {
        return $this->tokens[0] ?? null;
    }
}
