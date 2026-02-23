<?php

namespace DevRunner\Command;

use Ceedbox\LuneModule\LuneClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class GenerateLuneUrlCommand extends Command
{
    protected static $defaultName = 'generate';

    protected function configure(): void
    {
        $this
            ->setDescription('Generate Lune dashboard URL')
            ->addOption('org', null, InputOption::VALUE_OPTIONAL)
            ->addOption('client', null, InputOption::VALUE_OPTIONAL)
            ->addOption('secret', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $org = $input->getOption('org') ?? $this->ask(
            $input, $output, $helper,
            'Org ID', 'ORG123'
        );

        $client = $input->getOption('client') ?? $this->ask(
            $input, $output, $helper,
            'Client Handle', 'CLIENT1'
        );

        $secret = $input->getOption('secret') ?? $this->askHidden(
            $input, $output, $helper,
            'API Secret', 'secret'
        );

        $lune = new LuneClient(
            $org,
            $secret,
            'https://sustainability.lune.co'
        );

        $url = $lune->dashboardUrl($client);

        $output->writeln('');
        $output->writeln('<info>Generated URL:</info>');
        $output->writeln($url);
        $output->writeln('');

        return Command::SUCCESS;
    }

    private function ask($input, $output, $helper, string $label, string $default): string
    {
        $question = new Question("$label [$default]: ", $default);
        return $helper->ask($input, $output, $question);
    }

    private function askHidden($input, $output, $helper, string $label, string $default): string
    {
        $question = new Question("$label [$default]: ", $default);
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        return $helper->ask($input, $output, $question);
    }
}