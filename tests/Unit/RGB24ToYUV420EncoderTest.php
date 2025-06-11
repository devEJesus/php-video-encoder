<?php

use Devejesus\VideoEncoder\Encoder\RGB24ToYUV420Encoder;

test('encoder can be instantiated', function () {
    $encoder = new RGB24ToYUV420Encoder;
    expect($encoder)->toBeInstanceOf(RGB24ToYUV420Encoder::class);
});
