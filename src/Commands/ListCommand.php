<?php

/**
 * User: Dev_Lee
 * Date: 10/14/2023 - Time: 9:57 AM
 */


namespace Devlee\WakerCLI\Commands;

use Devlee\WakerCLI\Input\InputInterface;
use Devlee\WakerCLI\Output\OutputInterface;

/**
 * @author  Ankain Lesly <leeleslyank@gmail.com>
 * @package  Waker-CLI
 */

class ListCommand extends Command
{
  /**
   * @return void
   */
  protected function configure()
  {
    // $this->ignoreValidationErrors();
    $this
      ->setName('list')
      ->setDescription('Generate a list of all available commands in the application.')
      ->setHelp('This command allows you to Generate a list of all available commands in the application...')
      ->setHelp(
        <<<'EOF'
The <info>%command.name%</info> command displays a list of all available commands:

  <info>%command.full_name%</info>
EOF
      );
  }

  public function extraInfo($output)
  {
    $commands = $this->getApplication()->commands;

    // Options
    $output->newline();
    $output->writelnWarning('Available Commands: ');
    $output->newline();

    /**
     * @var Command $command
     */
    foreach ($commands as $command) {
      echo "  ";
      $output->writelnInfo($command->getName());
      $output->newGap();
      $output->writeln($command->getDescription());
    }
  }

  public function execute(InputInterface $input, OutputInterface $output): int
  {
    // You can use a custom command helper here
    $this->displayCommandInfo($output);
    return 0;
  }
}
