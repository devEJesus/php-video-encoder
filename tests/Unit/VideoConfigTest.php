<?php


use Devejesus\VideoEncoder\Class\VideoConfig;

test('video config can be initialized', function () {
    $config = new VideoConfig(384, 246);
    expect($config)->toBeInstanceOf(VideoConfig::class);
});

test('video config throws ArgumentCountError exception', function () {
    expect(fn () => new VideoConfig)->toThrow(ArgumentCountError::class);
});

test('video config throws invalid weight or height', function () {
    expect(fn () => new VideoConfig(243, 240))->toThrow(InvalidArgumentException::class, 'Width and height must be even numbers for YUV420 format.');
    expect(fn () => new VideoConfig(240, 243))->toThrow(InvalidArgumentException::class, 'Width and height must be even numbers for YUV420 format.');
    expect(fn () => new VideoConfig(241, 243))->toThrow(InvalidArgumentException::class, 'Width and height must be even numbers for YUV420 format.');
});
