<?php

namespace PStaender\SSShell;

use Psy\Command\Command;
use SilverStripe\Control\CLIRequestBuilder;
use SilverStripe\Control\HTTPApplication;
use SilverStripe\Core\CoreKernel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SakeCommand extends Command
{
    protected static $defaultName = 'sake';

    protected function configure()
    {
        $this
            ->setDescription('Run a sake command, e.g. sake dev/build')
            ->addArgument('url', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url = $input->getArgument('url');
        self::execute_silverstripe_url($url);
        return 0;
    }

    public static function execute_silverstripe_url($url = null, $flush = true)
    {
        // hacky way to force a parameter, but this seems to be the most efficient way here
        $_SERVER['REQUEST_URI'] = $url;
        $request = CLIRequestBuilder::createFromEnvironment();
        $kernel = new CoreKernel(BASE_PATH);
        if ($flush) {
            $kernel->boot($flush);
        }
        $app = new HTTPApplication($kernel);

        return $response = $app->handle($request);
    }

    public function getName(): ?string
    {
        return self::$defaultName ?? 'sake';
    }
}
