<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */


namespace Devlee\WakerCLI\Input;

use Devlee\WakerCLI\Exception\ConsoleException;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-CLI
 */

abstract class Input implements InputInterface
{
    /**
     * Option Value
     */
    public const OPTION_VALUE_NONE = 1;
    public const OPTION_VALUE_REQUIRED = 2;
    public const OPTION_VALUE_OPTIONAL = 4;

    /**
     * ArgumentS
     */
    public const ARGUMENT_REQUIRED = 1;
    public const ARGUMENT_OPTIONAL = 2;

    protected $definition = [];
    protected $options = [];
    protected $arguments = [];

    public function __construct($definition = null)
    {
        if ($definition) {
            $this->validate();
        }
    }

    /**
     * Processes command line arguments.
     *
     * @return void
     */
    abstract protected function parse();

    /**
     * @return void
     */
    public function setDefinition(array $definition)
    {
        $this->definition = $definition;
        $this->parse();

        $this->validate();
    }
    public function validate()
    {
        $missingArguments = array_filter(
            array_keys($this->definitionGetArgs()),
            fn ($argument) => !\array_key_exists($argument, $this->arguments) &&
                $this->definitionGetArg($argument)['mode'] === Input::ARGUMENT_REQUIRED
        );

        if (\count($missingArguments) > 0) {
            throw new ConsoleException(sprintf('Not enough arguments (missing: "%s").', implode(', ', $missingArguments)));
        }
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getArgument(string $name): mixed
    {
        return $this->arguments[$name] ?? false;
    }

    /**
     * @return void
     */
    public function setArgument(string $name, mixed $value)
    {
        $this->arguments[$name] = $value;
    }

    public function hasArgument(string $name): bool
    {
        return $this->arguments[$name] ? true : false;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getOption(string $name): mixed
    {
        return $this->options[$name] ?? false;
    }

    /**
     * @return void
     */
    public function setOption(string $name, mixed $value)
    {
        $this->options[$name] = $value;
    }

    public function hasOption(string $name): bool
    {
        return $this->options[$name] ? true : false;
    }


    /**
     * Gets an argument.
     */
    protected function definitionGetArg(string $name): array
    {
        return $this->definitionGetArgs()[$name] ?? [];
    }

    /**
     * Gets an option.
     */
    protected function definitionGetopt(string $name): array
    {
        return $this->definitionGetOpts()[$name] ?? [];
    }

    /**
     * Gets all argument.
     */
    protected function definitionGetArgs(): array
    {
        return $this->definition['arguments'] ?? [];
    }

    /**
     * Gets all options.
     */
    protected function definitionGetOpts(): array
    {
        return $this->definition['options'] ?? [];
    }

    /**
     * Get an option with a specific shortcut|flag.
     */
    protected function definitionGetOptFlag(string $flag): array|bool
    {
        foreach ($this->definitionGetOpts() as $key => $option) {
            if ($option['shortcut'] === $flag) {
                $option['_name'] = $key;
                return $option;
            }
        }

        return false;
    }
}
