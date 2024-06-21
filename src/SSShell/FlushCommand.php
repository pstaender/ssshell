<?php

namespace PStaender\SSShell;

use Psy\Command\Command;
use SilverStripe\Control\CLIRequestBuilder;
use SilverStripe\Control\HTTPApplication;
use SilverStripe\Core\CoreKernel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlushCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDescription('Runs flush command, e.g. for sake dev/build');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        CLIRequestBuilder::createFromEnvironment();
        $kernel = new CoreKernel(BASE_PATH);
        $kernel->boot(true);
        $output->writeln("<info>done</info>");
        return 0;
    }

    public function getName(): ?string
    {
        return 'flush';
    }
}
