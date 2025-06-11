<?php

namespace Devejesus\VideoEncoder\Encoder;

use Devejesus\VideoEncoder\Class\VideoConfig;
use Devejesus\VideoEncoder\Interface\EncoderInterface;

class RGB24ToYUV420Encoder implements EncoderInterface
{
    private VideoConfig $config;

    /** @var resource */
    private $inputHandler;

    private string $inputFile;

    /** @var resource */
    private $outputHandler;

    public function encode(string $inputFile, string $outputFile, VideoConfig $config): void
    {
        $this->config = $config;
        $this->inputFile = $inputFile;

        $this->openFiles($inputFile, $outputFile);

        $this->processFrames();

        $this->closeFiles();
    }

    private function openFiles(string $inputFile, string $outputFile): void
    {
        $input = fopen($inputFile, 'rb');

        if (! $input) {
            throw new \InvalidArgumentException('Failed to open input file: '.$inputFile);
        }
        $this->inputHandler = $input;

        $output = fopen($outputFile, 'wb');
        if (! $output) {
            fclose($this->inputHandler);
            throw new \InvalidArgumentException('Failed to open output file: '.$outputFile);
        }

        $this->outputHandler = $output;
    }

    private function closeFiles(): void
    {
        fclose($this->inputHandler);
        fclose($this->outputHandler);
        clearstatcache();
    }

    private function processFrames(): void
    {
        $width = $this->config->getWidth();
        $height = $this->config->getHeight();

        $pixelsPerFrame = $width * $height;
        $bytesPerRGBFrame = $pixelsPerFrame * 3;

        $inFileSizeBytes = filesize($this->inputFile);
        $totalFrames = intval($inFileSizeBytes / $bytesPerRGBFrame);

        $frameNumber = 0;

        while (! feof($this->inputHandler) && $frameNumber < $totalFrames) {
            $rgbFrame = $this->readRGBFrame($width, $height, $frameNumber);

            [$yPlane, $uPlane, $vPlane] = $this->generatePlanes($rgbFrame, $width, $height);

            fwrite($this->outputHandler, $yPlane);
            fwrite($this->outputHandler, $uPlane);
            fwrite($this->outputHandler, $vPlane);

            $frameNumber++;
        }
    }

    /**
     * @return array<int<0, max>, array<int<0, max>, array<int, int<0, 255>>>>
     */
    private function readRGBFrame(int $width, int $height, int $frameNumber): array
    {
        // Read entire RGB frame into memory
        $rgbFrame = [];
        for ($y = 0; $y < $height; $y++) {
            $rgbFrame[$y] = [];
            for ($x = 0; $x < $width; $x++) {
                $rgb = fread($this->inputHandler, 3);
                if (! $rgb || strlen($rgb) < 3) {
                    echo 'ERROR: Incomplete frame data at frame '.($frameNumber + 1)."\n";
                    break 2;
                }

                $rgbFrame[$y][$x] = [ord($rgb[0]), ord($rgb[1]), ord($rgb[2])];
            }
        }

        return $rgbFrame;
    }

    /**
     * @param  array<int<0, max>, array<int<0, max>, array<int, int<0, 255>>>>  $rgbFrame
     * @return string[]
     */
    private function generatePlanes(array $rgbFrame, int $width, int $height): array
    {
        // Generate Y plane (full resolution)
        $yPlane = '';
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $r = $rgbFrame[$y][$x][0];
                $g = $rgbFrame[$y][$x][1];
                $b = $rgbFrame[$y][$x][2];

                $Y = round(0.299 * $r + 0.587 * $g + 0.114 * $b);
                $Y = max(0, min(255, $Y));
                $yPlane .= chr((int) $Y);
            }
        }

        // Generate U and V planes (subsampled - 2x2 blocks averaged)
        $uPlane = '';
        $vPlane = '';

        for ($y = 0; $y < $height; $y += 2) {
            for ($x = 0; $x < $width; $x += 2) {
                // Average 2x2 block for chroma
                $totalR = 0;
                $totalG = 0;
                $totalB = 0;
                $count = 0;

                for ($dy = 0; $dy < 2; $dy++) {
                    for ($dx = 0; $dx < 2; $dx++) {
                        if (($y + $dy) < $height && ($x + $dx) < $width) {
                            $totalR += $rgbFrame[$y + $dy][$x + $dx][0];
                            $totalG += $rgbFrame[$y + $dy][$x + $dx][1];
                            $totalB += $rgbFrame[$y + $dy][$x + $dx][2];
                            $count++;
                        }
                    }
                }

                if ($count > 0) {
                    $avgR = $totalR / $count;
                    $avgG = $totalG / $count;
                    $avgB = $totalB / $count;

                    $U = round(-0.14713 * $avgR - 0.28886 * $avgG + 0.436 * $avgB + 128);
                    $V = round(0.615 * $avgR - 0.51499 * $avgG - 0.10001 * $avgB + 128);

                    $U = max(0, min(255, $U));
                    $V = max(0, min(255, $V));

                    $uPlane .= chr((int) $U);
                    $vPlane .= chr((int) $V);
                }
            }
        }

        return [$yPlane, $uPlane, $vPlane];
    }
}
