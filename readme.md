# PHP Video Encoder

A simple PHP-based video encoder that converts raw RGB24 frames to YUV420p format using custom pixel processing.

## Features

- Convert RGB24 frames to YUV420p format
- Efficient pixel processing
- Simple and easy-to-use interface
- No external dependencies required

## Requirements

- PHP >= 8.0

## Installation

You can install the package via composer:

```bash
composer require devejesus/php-video-encoder
```

## Usage

```php
use Devejesus\PhpVideoEncoder\Encoder\RGB24ToYUV420Encoder;
use Devejesus\PhpVideoEncoder\Model\VideoConfig;

// Create a video configuration
$config = new VideoConfig(384, 216); // width and height must be even numbers

// Initialize the encoder
$encoder = new RGB24ToYUV420Encoder();

// Convert the video
try {
    $encoder->encode(
        'input.rgb24',  // Input file path (RGB24 format)
        'output.yuv',   // Output file path (YUV420p format)
        $config
    );
    echo "Conversion completed successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

## Input Format Requirements

- The input file must be in RGB24 format
- Width and height must be even numbers
- Each pixel is represented by 3 bytes (R, G, B)
- No headers or metadata in the file

## Testing

```bash
composer test
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This package is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.