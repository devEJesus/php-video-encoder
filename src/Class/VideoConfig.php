<?php

namespace Devejesus\VideoEncoder\Class;

class VideoConfig
{
    private int $width;

    private int $height;

    public function __construct(int $width, int $height)
    {
        $this->validateDimensions($width, $height);
        $this->width = $width;
        $this->height = $height;
    }

    private function validateDimensions(int $width, int $height): void
    {
        if ($width % 2 !== 0 || $height % 2 !== 0) {
            throw new \InvalidArgumentException(
                'Width and height must be even numbers for YUV420 format.'
            );
        }
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }
}
