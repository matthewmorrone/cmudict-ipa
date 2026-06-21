<?php

namespace CmuDictIpa\Command;

use CmuDictIpa\Service\PhoneticConverterService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ConvertCommand extends Command
{
    protected static $defaultName = 'convert';
    private PhoneticConverterService $converter;

    protected function configure(): void
    {
        $this
            ->setDescription('Convert between CMU Dictionary format and IPA')
            ->addArgument('input', InputArgument::REQUIRED, 'Input file path')
            ->addArgument('output', InputArgument::REQUIRED, 'Output file path')
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Output format (tsv, json, xml)', 'tsv')
            ->addOption('reverse', 'r', InputOption::VALUE_NONE, 'Convert from IPA to CMU format')
            ->addOption('no-stress', null, InputOption::VALUE_NONE, 'Exclude stress markers')
            ->addOption('batch', 'b', InputOption::VALUE_NONE, 'Process multiple files (input should be a directory)')
            ->addOption('mapping', 'm', InputOption::VALUE_OPTIONAL, 'Custom mapping file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Setup logging
            $logger = new Logger('converter');
            $logger->pushHandler(new StreamHandler(__DIR__ . '/../../var/logs/app.log', Logger::INFO));

            // Load configuration
            $config = require __DIR__ . '/../../config/config.php';
            
            // Override mapping file if provided
            if ($input->getOption('mapping')) {
                $config['mapping']['default_file'] = $input->getOption('mapping');
            }

            $this->converter = new PhoneticConverterService($config, $logger);

            $inputPath = $input->getArgument('input');
            $outputPath = $input->getArgument('output');
            $format = $input->getOption('format');
            $isReverse = $input->getOption('reverse');
            $includeStress = !$input->getOption('no-stress');
            $isBatch = $input->getOption('batch');

            if ($isBatch) {
                $this->processBatch($inputPath, $outputPath, $format, $isReverse, $includeStress);
            } else {
                $this->processFile($inputPath, $outputPath, $format, $isReverse, $includeStress);
            }

            $output->writeln('<info>Conversion completed successfully!</info>');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    private function processFile(string $inputPath, string $outputPath, string $format, bool $isReverse, bool $includeStress): void
    {
        if (!file_exists($inputPath)) {
            throw new \RuntimeException("Input file not found: {$inputPath}");
        }

        $content = file_get_contents($inputPath);
        $lines = explode("\n", $content);
        $results = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if ($isReverse) {
                $results[] = $this->converter->convertFromIpa($line);
            } else {
                $results[] = $this->converter->convertToIpa($line, $includeStress);
            }
        }

        $this->writeOutput($results, $outputPath, $format);
    }

    private function processBatch(string $inputDir, string $outputDir, string $format, bool $isReverse, bool $includeStress): void
    {
        if (!is_dir($inputDir)) {
            throw new \RuntimeException("Input directory not found: {$inputDir}");
        }

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $files = glob($inputDir . '/*.{txt,tsv}', GLOB_BRACE);
        foreach ($files as $file) {
            $basename = basename($file, '.' . pathinfo($file, PATHINFO_EXTENSION));
            $outputPath = $outputDir . '/' . $basename . '.' . $format;
            $this->processFile($file, $outputPath, $format, $isReverse, $includeStress);
        }
    }

    private function writeOutput(array $results, string $outputPath, string $format): void
    {
        switch ($format) {
            case 'json':
                file_put_contents($outputPath, json_encode($results, JSON_PRETTY_PRINT));
                break;
            case 'xml':
                $xml = new \SimpleXMLElement('<conversions/>');
                foreach ($results as $result) {
                    $xml->addChild('entry', $result);
                }
                $xml->asXML($outputPath);
                break;
            case 'tsv':
            default:
                file_put_contents($outputPath, implode("\n", $results));
                break;
        }
    }
} 