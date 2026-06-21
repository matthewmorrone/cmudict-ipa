<?php

namespace CmuDictIpa\Tests\Unit;

use CmuDictIpa\Service\PhoneticConverterService;
use PHPUnit\Framework\TestCase;
use Monolog\Logger;
use Monolog\Handler\NullHandler;

class PhoneticConverterServiceTest extends TestCase
{
    private PhoneticConverterService $converter;
    private array $testConfig;

    protected function setUp(): void
    {
        $logger = new Logger('test');
        $logger->pushHandler(new NullHandler());

        $this->testConfig = [
            'mapping' => [
                'default_file' => __DIR__ . '/../../data/mappings/ipa.tsv'
            ]
        ];

        $this->converter = new PhoneticConverterService($this->testConfig, $logger);
    }

    public function testConvertToIpa(): void
    {
        $input = 'AE1 B AH0 V';
        $expected = 'ˈæ b ə';
        $result = $this->converter->convertToIpa($input);
        $this->assertEquals($expected, $result);
    }

    public function testConvertToIpaWithoutStress(): void
    {
        $input = 'AE1 B AH0 V';
        $expected = 'æ b ə';
        $result = $this->converter->convertToIpa($input, false);
        $this->assertEquals($expected, $result);
    }

    public function testConvertFromIpa(): void
    {
        $input = 'ˈæ b ə';
        $expected = 'AE1 B AH0';
        $result = $this->converter->convertFromIpa($input);
        $this->assertEquals($expected, $result);
    }

    public function testUnknownPhoneme(): void
    {
        $input = 'XX1 YY ZZ';
        $result = $this->converter->convertToIpa($input);
        $this->assertEquals('XX1 YY ZZ', $result);
    }
} 