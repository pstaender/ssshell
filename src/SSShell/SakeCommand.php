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
        self::execute_silverstripe_url($url ?: '', input: $input, output: $output);
        return 0;
    }

    public static function execute_silverstripe_url($url, InputInterface $input = null, OutputInterface $output = null)
    {
        // hacky way to force a parameter, but this seems to be the most efficient way here
        $_SERVER['REQUEST_URI'] = $url;
        $request = CLIRequestBuilder::createFromEnvironment();
        $kernel = new CoreKernel(BASE_PATH);

        $app = new HTTPApplication($kernel);

        $response = $app->handle($request);

        if (!empty($url) && $output) {
            if ($response->getStatusCode() !== 200) {
                $output->writeln("<error>ERROR</error> ".$response->getBody());
            } else {
                echo $response->getBody();
            }
        }
        // var_dump($response->getBody());
        return;
    }

    public function getName(): ?string
    {
        return self::$defaultName ?? 'sake';
    }
}
