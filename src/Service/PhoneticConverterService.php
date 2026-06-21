<?php

namespace CmuDictIpa\Service;

use Psr\Log\LoggerInterface;
use RuntimeException;

class PhoneticConverterService
{
    private array $mapping = [];
    private array $config;
    private LoggerInterface $logger;

    public function __construct(array $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
        $this->initializeMapping();
    }

    private function initializeMapping(): void
    {
        $mappingFile = $this->config['mapping']['default_file'];
        if (!file_exists($mappingFile)) {
            throw new RuntimeException("Mapping file not found: {$mappingFile}");
        }

        $content = file_get_contents($mappingFile);
        if ($content === false) {
            throw new RuntimeException("Failed to read mapping file");
        }

        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            $parts = explode("\t", $line);
            if (count($parts) !== 2) continue;
            
            [$from, $to] = array_map('trim', $parts);
            $this->mapping[$from] = $to;
        }

        // Add stress markers
        $this->mapping['1'] = 'ˈ';  // Primary stress
        $this->mapping['2'] = 'ˌ';  // Secondary stress
    }

    public function convertToIpa(string $input, bool $includeStress = true): string
    {
        $words = preg_split('/\s+/', trim($input));
        $result = '';
        
        foreach ($words as $word) {
            $phonemes = preg_split('/(?<=\d)|(?=\d)/', $word);
            $ipaWord = '';
            
            foreach ($phonemes as $phoneme) {
                $phoneme = trim($phoneme);
                if (empty($phoneme)) continue;
                
                if (isset($this->mapping[$phoneme])) {
                    $converted = $this->mapping[$phoneme];
                    if (!$includeStress && ($converted === 'ˈ' || $converted === 'ˌ')) {
                        continue;
                    }
                    $ipaWord .= $converted;
                } else {
                    $this->logger->warning("Unknown phoneme: {$phoneme}");
                    $ipaWord .= $phoneme;
                }
            }
            
            $result .= $ipaWord . ' ';
        }
        
        return trim($result);
    }

    public function convertFromIpa(string $ipa): string
    {
        $reverseMapping = array_flip($this->mapping);
        $result = '';
        
        $chars = preg_split('//u', $ipa, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $char) {
            if (isset($reverseMapping[$char])) {
                $result .= $reverseMapping[$char] . ' ';
            } else {
                $this->logger->warning("Unknown IPA character: {$char}");
                $result .= $char . ' ';
            }
        }
        
        return trim($result);
    }

    public function getMapping(): array
    {
        return $this->mapping;
    }
} 