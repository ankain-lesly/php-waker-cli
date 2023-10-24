<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */

namespace Devlee\WakerCLI;

use Devlee\WakerCLI\Commands\Command;
use Devlee\WakerCLI\Commands\GreetingsCommand;
use Devlee\WakerCLI\Commands\HelpCommand;
use Devlee\WakerCLI\Commands\ListCommand;
use Devlee\WakerCLI\Exception\ConsoleException;
use Devlee\WakerCLI\Input\ArgvInput;
use Devlee\WakerCLI\Input\InputInterface;
use Devlee\WakerCLI\Output\ConsoleOutput;
use Devlee\WakerCLI\Output\OutputInterface;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-CLI
 */

class WakerCLI
{
  public array $commands = [];
  private string $defaultName = 'list';
  private bool $initialized = false;
  private static Command $currentCommand;
  private ?string $signature = null;

  public function __construct(private string $name = "APP:NAME", private string $version = 'APP:VERSION')
  {
    $this->boot();
  }

  /**
   * Registers a new command.
   * @method register
   */
  public function register(string $name): Command
  {
    return $this->add(new Command($name));
  }

  /**
   * Adds an array of command objects.
   *
   * @param Command[] $commands An array of commands
   * @method addCommands
   */
  public function addCommands(array $commands)
  {
    foreach ($commands as $command) {
      $this->add($command);
    }
  }

  /**
   * Adds a command object.
   *
   * @return Command|null
   */
  public function add(Command $command)
  {
    $this->boot();

    $command->setApplication($this);

    $this->commands[$command->getName()] = $command;

    return $command;
  }

  /**
   * Returns a registered command
   *
   * @return Command
   *
   * @throws ConsoleException When given command name does not exist
   */
  public function get(string $name)
  {
    $this->boot();

    $command = $this->commands[$name] ?? false;
    if (!$command) {
      throw new ConsoleException(sprintf('The command "%s" does not exist.', $name));
    }

    return $command;
  }

  /**
   * Returns true if the command exists, false otherwise.
   */
  public function has(string $name): bool
  {
    $this->boot();
    return $this->commands[$name] ?? false;
  }

  /**
   * Gets the default commands .
   *
   * @return Command[]
   */

  protected function getDefaultCommands(): array
  {
    return [new HelpCommand(), new ListCommand(), new GreetingsCommand()];
  }

  /**
   * Initialize application signature
   *
   */
  public function setSignature(string $signature): void
  {
    $this->signature = $signature;
  }

  /**
   * Returns application signature
   */
  public function getSignature(string $signature): ?string
  {
    return $this->signature;
  }
  /**
   * Runs the current application.
   *
   * @return int 0 if everything went fine, or an error code
   *
   */
  public function run(InputInterface $input = null, OutputInterface $output = null): int
  {
    try {

      $input ??= new ArgvInput();
      $output ??= new ConsoleOutput();

      $command_name = $input->getFirstArgument() ?? $this->defaultName;
      $command = $this->get($command_name);
      self::$currentCommand = $command;
      $input->setDefinition($command->getDefinition());

      if ($input->getOption('help')) {
        $command->displayCommandInfo($output);
        $output->newline();

        $output->writelnWarning("Help");
        $help = $command->getProcessedHelp();
        $output->newline();
        $output->writeln($help);

        return 1;
      } elseif ($input->getOption('name')) {
        $output->newline();
        $output->newline();
        $output->writelnWarning(">> \t App name: ");
        $output->writelnWarning($this->name);
        $output->newline();
        $output->newline();
        return 1;
      } elseif ($input->getOption('version')) {
        $output->newline();
        $output->newline();
        $output->writelnWarning(">> \t App Version: ");
        $output->writelnWarning($this->version);
        $output->newline();
        $output->newline();
        return 1;
      }

      $renderException = function (\Throwable $e) use ($output) {
        $this->renderThrowable($e, $output);
      };

      if ($phpHandler = set_exception_handler($renderException)) {
        restore_exception_handler();
        if (!\is_array($phpHandler)) {
          $errorHandler = true;
        } elseif ($errorHandler = $phpHandler[0]->setExceptionHandler($renderException)) {
          $phpHandler[0]->setExceptionHandler($errorHandler);
        }
      }

      return $command->execute($input, $output);
    } catch (\Throwable $th) {
      $this->renderThrowable($th, $output);
      return Command::FAILURE;
    }
  }

  private function boot(): void
  {
    if ($this->initialized) {
      return;
    }
    $this->initialized = true;

    foreach ($this->getDefaultCommands() as $command) {
      $this->add($command);
    }
  }
  public static function getCurrentCommandHintText()
  {
    if (isset(self::$currentCommand)) {
      return self::$currentCommand->getHintText();
    }
  }
  private function renderThrowable(\Throwable $e, OutputInterface $output): void
  {
    $output->error($e->getMessage());
    $hint = (string) static::getCurrentCommandHintText();

    if ($hint) {
      $output->writelnInfo($hint);
      $output->newline();
    }
    // print_r($e);
    // exit;
  }
}
