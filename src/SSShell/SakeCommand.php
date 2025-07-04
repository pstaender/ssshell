<?php

namespace PStaender\SSShell;

use Psy\Command\Command;
use SilverStripe\Cli\Sake;
use SilverStripe\Control\CLIRequestBuilder;
use SilverStripe\Control\HTTPApplication;
use SilverStripe\Core\CoreKernel;
use SilverStripe\ORM\DB;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SakeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setDescription('Run a sake command, e.g. sake db:build')
            ->addOption('verbose', ['v', 'vv', 'vvv'])
            ->addOption('flush', 'f')
            ->addOption('quiet', 'q')
            ->addOption('version', 'V')
            ->addOption('silent')
            ->addOption('ansi')
            ->addOption('no-ansi')
            ->addOption('no-interaction')
            ->addOption('no-database')
            ->setHelp('This command allows you to run any sake command from the command line.')
            ->addArgument('arg', InputArgument::IS_ARRAY, 'The sake command to run, e.g. "db:build" or "dev/tasks/MyTask"');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        DB::setMustUsePrimary();
        $sake = new Sake();
        $args = $input->getArguments()["arg"];
        $options = [
            $input->getOption('verbose') ? '-vvv' : null,
            $input->getOption('version') ? '--version' : null,
            $input->getOption('flush') ? '--flush' : null,
            $input->getOption('silent') ? '--silent' : null,
            $input->getOption('quiet') ? '--quiet' : null,
            $input->getOption('ansi') ? '--ansi' : null,
            $input->getOption('no-ansi') ? '--no-ansi' : null,
            $input->getOption('no-interaction') ? '--no-interaction' : null,
            $input->getOption('no-database') ? '--no-database' : null,
        ];
        $options = array_filter($options); // Remove null values
        $options = array_filter($options, fn($option) => $option !== '--help');
        $args = array_merge($options, $args);
        array_unshift($args, 'sake'); // Add 'sake' as the first argument to match the expected command structure
        $sake->setAutoExit(false);
        return $sake->run(new ArgvInput($args));
    }

    public static function execute_silverstripe_url($url, ?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        // hacky way to force a parameter, but this seems to be the most efficient way here
        $_SERVER['REQUEST_URI'] = $url;
        $request = CLIRequestBuilder::createFromEnvironment();
        $kernel = new CoreKernel(BASE_PATH);

        $app = new HTTPApplication($kernel);

        $response = $app->handle($request);

        if (!empty($url)) {
            if ($response->getStatusCode() >= 400 && $output) {
                $output->writeln("<error> Error " . $response->getStatusCode() . " </error>");
                echo ($output->getVerbosity() <= 32 && strlen($response->getBody()) > 160 ? substr($response->getBody(), 0, 160) . '…' : $response->getBody()) . "\n";
                return $response->getStatusCode();
            } else {
                echo $response->getBody();
                return 0;
            }
        }
        return $response->getStatusCode() >= 400 ? $response->getStatusCode() : 0;
    }

    public function getName(): ?string
    {
        return 'sake';
    }
}
