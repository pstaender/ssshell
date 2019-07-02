<?php
namespace PStaender\SSShell;

use Psy\Command\Command;
use SilverStripe\Control\CLIRequestBuilder;
use SilverStripe\Control\HTTPApplication;
use SilverStripe\Core\CoreKernel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SilverStripeSakeCommand extends Command
{
    protected static $defaultName = 'sake';

    protected function configure()
    {
        $this
            ->setDescription('Runs a sake command, e.g. sake dev/build')
            ->addArgument('url', InputArgument::REQUIRED, 'URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        self::execute_silverstripe_url($url);
    }

    public static function execute_silverstripe_url($url = null, $flush = true)
    {

        $_SERVER['REQUEST_URI'] = $url;
        // Build request and detect flush
        $request = CLIRequestBuilder::createFromEnvironment();

        // Default application
        $kernel = new CoreKernel(BASE_PATH);
        $kernel->boot($flush);
        $app = new HTTPApplication($kernel);
        return $response = $app->handle($request);
    }
}
