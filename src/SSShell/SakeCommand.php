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
    protected function configure()
    {
        $this
            ->setDescription('Run a sake command, e.g. sake dev/build')
            ->addArgument('url', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $url = $input->getArgument('url');
        return self::execute_silverstripe_url($url ?: '', input: $input, output: $output);
    }

    public static function execute_silverstripe_url($url, InputInterface $input = null, OutputInterface $output = null): int
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
