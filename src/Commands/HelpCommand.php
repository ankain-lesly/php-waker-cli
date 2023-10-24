<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */


namespace Devlee\WakerCLI\Commands;

use Devlee\WakerCLI\Input\Input;
use Devlee\WakerCLI\Input\InputInterface;
use Devlee\WakerCLI\Output\OutputInterface;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-CLI
 */

class HelpCommand extends Command
{
  private Command $command;
  /**
   * @return void
   */
  protected function configure()
  {
    // $this->ignoreValidationErrors();
    $this
      ->setName('help')
      ->setHintText('help <command_name> [--options]')
      ->setDescription('Display help for the given command. When no command is given display help for the list command')
      ->addArgument('command_name', Input::ARGUMENT_OPTIONAL, 'Name of the command to display help information')
      ->setHelp(
        <<<'EOF'
The <info>%command.name%</info> command displays help for a given command:

  <info>%command.full_name% list</info>

You can also output the help in other formats by using the <comment>--format</comment> option:

  <info>%command.full_name% --format=xml list</info>

To display the list of available commands, please use the <info>list</info> command.
EOF
      );
  }

  public function execute(InputInterface $input, OutputInterface $output): int
  {
    // You can use a custom command helper here
    $commandName = $input->getArgument('command_name') ?: $this->getName();
    $this->command = $this->getApplication()->get($commandName) ?? $this;
    $this->command->displayCommandInfo($output);

    $text = $this->command->getProcessedHelp();
    $output->newline();
    $output->writelnWarning("Help: ");
    $output->newline();
    $output->writeln($text);

    return 0;
  }
}
