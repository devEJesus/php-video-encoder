<?php

use Devejesus\VideoEncoder\Class\VideoConfig;
use Devejesus\VideoEncoder\Encoder\RGB24ToYUV420Encoder;

require_once __DIR__.'/../vendor/autoload.php';

$config = new VideoConfig(384, 216);
$encoder = new RGB24ToYUV420Encoder;

try {
    $encoder->encode(
        __DIR__.'/video.rgb24',
        __DIR__.'/output.yuv',
        $config
    );
    echo "Conversion completed successfully!\n";
} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
