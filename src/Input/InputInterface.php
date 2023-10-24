<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */


namespace Devlee\WakerCLI\Input;

/**
 * 
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-CLI
 *
 */
interface InputInterface
{
    public function setDefinition(array $definition);
    public function validate();
    public function getArguments(): array;
    public function getArgument(string $name): mixed;
    public function setArgument(string $name, mixed $value);
    public function hasArgument(string $name): bool;
    public function getOptions(): array;
    public function getOption(string $name): mixed;
    public function setOption(string $name, mixed $value);
    public function hasOption(string $name): bool;
    public function getFirstArgument(): ?string;
}
