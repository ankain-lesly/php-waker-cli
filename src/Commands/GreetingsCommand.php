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

class GreetingsCommand extends Command
{
  /**
   * @return void
   */
  protected function configure()
  {
    // $this->ignoreValidationErrors();
    $this
      ->setName('greet')
      ->setHintText('greet <username>')
      ->setDescription('Greet a user')
      ->setHelp("This command allows you to greet a user based on the time of the day... \n >> php waker greet <username> --caps ")
      ->addArgument('username', Input::ARGUMENT_REQUIRED, 'The username of the user.')
      ->addOption(
        'caps',
        true,
        Input::OPTION_VALUE_NONE,
        'Change the greeting text in uppercase'
      )
      ->addOption(
        'when',
        true,
        Input::OPTION_VALUE_REQUIRED,
        'Decide when to greet'
      );
  }

  public function execute(InputInterface $input, OutputInterface $output): int
  {
    $message =  "Waker Says >> Greetings " . $input->getArgument('username');

    if ($input->getOption('when')) {
      $message .= "; when? " . $input->getOption('when');
    }

    if ($input->getOption('caps')) {
      $message = strtoupper($message);
    }
    $output->newline();
    $output->writelnWarning($message);
    $output->newline();
    return 0;
  }
}
