<?php

namespace Devejesus\VideoEncoder\Interface;

use Devejesus\VideoEncoder\Class\VideoConfig;

interface EncoderInterface
{
    public function encode(string $inputFile, string $outputFile, VideoConfig $config): void;
}
