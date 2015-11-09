<?php

namespace Mongrate\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends Command
{
    protected function configure()
    {
        $this->setName('self-update')
            ->setDescription('Replace the current executable with the latest version from mongrate.com.')
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (ini_get('allow_url_fopen') !== '1') {
            throw new \RuntimeException('`allow_url_fopen` must be enabled in your php.ini to use the `self-update` command.');
        }

        $phar = preg_replace('/^phar:\/\//', '', \Phar::running());
        $output->writeln('Overwriting ' . $phar);
        if (!is_writable($phar)) {
            throw new \RuntimeException('The Phar path is not writable. Ensure permissions are set to allow the file to be written to.');
        }

        $latest = file_get_contents('http://mongrate.com/download?self-update&from=' . MONGRATE_VERSION);
        file_put_contents($phar, $latest);
    }
}
