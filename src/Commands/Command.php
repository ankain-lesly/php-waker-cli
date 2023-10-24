<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */


namespace Devlee\WakerCLI\Commands;

use Devlee\WakerCLI\WakerCLI;
use Devlee\WakerCLI\Exception\ConsoleException;
use Devlee\WakerCLI\Input\ArgvInput;
use Devlee\WakerCLI\Input\Input;
use Devlee\WakerCLI\Output\OutputInterface;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-CLI
 */

abstract class Command
{
  public const SUCCESS = 0;
  public const FAILURE = 1;
  public const INVALID = 2;

  private ?WakerCLI $application = null;
  private ?string $name = null;
  private string $description = '';
  private array $definition = [];
  private ?string $hintText = null;
  private ?string $help = null;

  public function __construct(string $name = null)
  {
    if (null !== $name) {
      $this->setName($name);
    }

    $this->configure();
    $this->addGlobalOptions();
  }

  /**
   * @return void
   */
  public function setApplication(WakerCLI $application = null)
  {
    $this->application = $application;
  }
  /**
   * Gets the application instance for this command.
   */
  public function getApplication(): ?WakerCLI
  {
    return $this->application;
  }

  /**
   * Configures the current command.
   *
   * @return void
   */
  abstract protected function configure();

  /**
   * Executes the current command.
   *
   * @return int 0 if everything went fine, or an exit code
   *
   */
  abstract public function execute(ArgvInput $input, OutputInterface $output): int;

  /**
   * Adds an argument.
   * 
   * @return $this 
   */
  public function addArgument(string $name, int $mode = null, string $description = ''): static
  {
    $this->definition['arguments'][$name] = [
      'mode' => $mode,
      'description' => $description,
    ];
    return $this;
  }

  /**
   * Adds an option.
   * 
   * @return $this 
   */
  public function addOption(string $name, ?bool $shortcut = null, int $mode = null, string $description = ''): static
  {
    if ($shortcut) {
      $shortcut = $name[0];
    };

    $this->definition['options'][$name] = [
      'shortcut' => $shortcut,
      'mode' => $mode,
      'description' => $description,
    ];
    return $this;
  }
  /**
   * Sets the name of the command.
   * 
   * @return $this 
   */
  public function setName(string $name): static
  {
    $this->validateName($name);

    $this->name = $name;

    return $this;
  }

  /**
   * Sets the command hintText.
   *
   * @return $this
   */
  public function setHintText(string $hintText): static
  {
    $this->hintText = 'php waker ' . $hintText;

    return $this;
  }

  /**
   * Get command hintText.
   *
   * @return $this
   */
  public function getHintText(): ?string
  {
    return $this->hintText;
  }

  /**
   * Sets the description for the command.
   *
   * @return $this
   */
  public function setDescription(string $description): static
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Returns the description for the command.
   */
  public function getDescription(): string
  {
    return $this->description;
  }
  /**
   * Returns the description for the command.
   */
  public function getDefinition(): array
  {
    return $this->definition;
  }

  /**
   * Sets the help for the command.
   *
   * @return $this
   */
  public function setHelp(string $help): static
  {
    $this->help = $help;

    return $this;
  }

  /**
   * Returns the help for the command.
   */
  public function getHelp(): string
  {
    return $this->help;
  }

  /**
   * Returns the processed help for the command replacing the %command.name% and
   * %command.full_name% patterns with the real values dynamically.
   */
  public function getProcessedHelp(?string $text_data = null): string
  {
    if (!$text_data) {
      $text_data = $this->help;
    }
    $placeholders = [
      '%command.name%',
      '%command.full_name%',
    ];
    $replacements = [
      $this->name,
      'php ' . $_SERVER['PHP_SELF'] . ' ' . $this->name,
    ];

    return str_replace($placeholders, $replacements, $text_data ?: $this->getDescription());
  }
  /**
   * Returns the description for the command.
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * Validates a command name.
   *
   * It must be non-empty and parts can optionally be separated by ":".
   */
  private function validateName(string $name): void
  {
    if (!preg_match('/^[^\:]++(\:[^\:]++)*$/', $name)) {
      throw new ConsoleException(sprintf('Command name "%s" is invalid.', $name));
    }
  }

  /**
   * Global Options
   * 
   * @return array
   */
  private function addGlobalOptions(): void
  {
    foreach ($this->getGlobalOptions() as $key => $value) {
      $options = $this->definition['options'] ?? [];
      echo $isThere = array_key_exists($key, $options);
      if (!$isThere) {
        $this->definition['options'][$key] = $value;
      }
    }
  }

  /**
   * Global Options
   * 
   * @return array
   */
  private function getGlobalOptions(): array
  {
    $options = array(
      'help' => [
        'shortcut' => 'h',
        'mode' => Input::OPTION_VALUE_NONE,
        'description' => "Display help for the given command",
      ],
      'name' => [
        'shortcut' => 'n',
        'mode' => Input::OPTION_VALUE_NONE,
        'description' => "Display the current application name",
      ],
      'version' => [
        'shortcut' => 'v',
        'mode' => Input::OPTION_VALUE_NONE,
        'description' => "Display the application version",
      ],
    );
    return $options;
  }

  /**
   * Generate Native command help meta data
   * 
   * @return void;
   *
   */
  public function displayCommandInfo(OutputInterface $output)
  {
    $output->writelnInfo('>> Running CLI Tool...');
    $output->newline();
    $output->newline();
    // Description
    $output->writelnWarning('Description: ');
    $output->newline();
    echo "  ";
    $output->writeln($this->getDescription());
    // Description
    $output->newline();
    $output->writelnWarning('Usage: ');
    $usage = $this->getHintText() ?? "php waker" . $this->getName() . ' <arguments> [--options]';
    $output->newline();
    echo "  ";
    $output->writeln($usage);

    // Arguments
    if ($arguments = $this->getDefinition()['arguments'] ?? false) {
      $output->newline();
      $output->writelnWarning('Arguments: ');
      $output->newline();
      foreach ($arguments as $name => $value) {
        echo "  ";
        $output->writelnInfo($name);
        $output->newGap();
        $output->writeln($value['description']);
      }
    }

    // Options
    if ($options = array_merge($this->getDefinition()['options'] ?? [], $this->getGlobalOptions())) {
      $output->newline();
      $output->writelnWarning('Options: ');
      $output->newline();
      foreach ($options as $name => $value) {
        echo "  ";
        if ($value['shortcut']) {
          $output->writelnInfo('-' . $name[0]);
        }
        $output->newGap();
        $output->writelnInfo('--' . $name);
        $output->newGap();
        $output->writeln($value['description']);
      }
    }

    // If Command Implements this method it will be executed
    $methodName = 'extraInfo';
    if (method_exists($this, $methodName)) {
      $this->$methodName($output);
    }
  }
}
