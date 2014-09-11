<?php

$version = 'Prototype';

// -----------------------------------------------------------------------------
// Receive parameters
$rawParameters = filter_input_array(INPUT_GET);
if ($rawParameters === null) {

    $rawParameters = array();
}

$shortCutMap = array(
    'w' => 'width',
    'h' => 'height',
    'f' => 'format',
    't' => 'text',
    'bc' => 'background-color',
    'tc' => 'text-color',
    'tbc' => 'text-background-color'
);

$parameters = array();
foreach ($rawParameters as $key => $value) {

    if (array_key_exists($key, $shortCutMap)) {

        $key = $shortCutMap[$key];
    }
    $parameters[$key] = $value;
}

$width = array_key_exists('width', $parameters)
    ? $parameters['width']
    : null;

$height = array_key_exists('height', $parameters)
    ? $parameters['height']
    : null;

$format = array_key_exists('format', $parameters)
    ? $parameters['format']
    : 'png';

$text = array_key_exists('text', $parameters)
    ? $parameters['text']
    : null;

$backgroundColor  = array_key_exists('background-color', $parameters)
    ? $parameters['background-color']
    : '000000';

$textColor  = array_key_exists('text-color', $parameters)
    ? $parameters['text-color']
    : 'FFFFFF';

$textBackgroundColor  = array_key_exists('text-background-color', $parameters)
    ? $parameters['text-background-color']
    : $backgroundColor;

// -----------------------------------------------------------------------------
// Validate parameters
$messages = array();
if ($height === null || !ctype_digit($height) || $height <= 0) {

    $messages[] = 'Height must be given as integer greater zero!';
}

if ($width === null || !ctype_digit($width) || $width <= 0) {

    $messages[] = 'Width must be given as integer greater zero!';
}

if (!in_array($format, array('png'))) {

    $messages[] = 'Format not supported';
}

if (preg_match('/^[0-9A-F]{6}$/', $backgroundColor) != 1) {

    $messages[] = 'Invalid color format for background-color! I need a 6 digit hex value: FEDC12';
}

if (preg_match('/^[0-9A-F]{6}$/', $textColor) != 1) {

    $messages[] = 'Invalid color format for text-color! I need a 6 digit hex value: FEDC12';
}

if (preg_match('/^[0-9A-F]{6}$/', $textBackgroundColor) != 1) {

    $messages[] = 'Invalid color format for text-background-color! I need a 6 digit hex value: FEDC12';
}

if (!empty($messages)) {


    $serverParameters = filter_input_array(INPUT_SERVER);

    print('Errors:');
    print('<br />');
    foreach ($messages as $message) {

        print($message);
        print('<br />');
    }
    print('<br />');

    print('Available Parameters:');
    print('<br />');
    foreach ($shortCutMap as $short => $long) {

        print("{$long}, ({$short})");
        print('<br />');
    }
    print('<br />');

    $baseUrl = 'http://' . $serverParameters['HTTP_HOST'];

    print("Example:");
    print('<br />');
    $exampleUris[] = "{$baseUrl}?width=400&height=100";
    $exampleUris[] = "{$baseUrl}?width=400"
        . "&height=100"
        . "&format=png"
        . "&background-color=FF8800"
        . "&text-color=FFFFFF"
        . "&text-background-color=000000";

    $exampleUris[] = "{$baseUrl}?w=400"
        . "&h=100"
        . "&f=png"
        . "&bc=FF8800"
        . "&t=test"
        . "&tc=FFFFFF"
        . "&tbc=000000";

    foreach ($exampleUris as $exampleUri) {

        print("<a href=\"{$exampleUri}\" >{$exampleUri}</a>");
        print('<br />');
    }

    exit();
}
// -----------------------------------------------------------------------------
// Generate image

$bca = array(
    'r' => (integer) hexdec(substr($backgroundColor, 0, 2)),
    'g' => (integer) hexdec(substr($backgroundColor, 2, 2)),
    'b' => (integer) hexdec(substr($backgroundColor, 4, 2))
);

$tca = array(
    'r' => (integer) hexdec(substr($textColor, 0, 2)),
    'g' => (integer) hexdec(substr($textColor, 2, 2)),
    'b' => (integer) hexdec(substr($textColor, 4, 2))
);

$tbca = array(
    'r' => (integer) hexdec(substr($textBackgroundColor, 0, 2)),
    'g' => (integer) hexdec(substr($textBackgroundColor, 2, 2)),
    'b' => (integer) hexdec(substr($textBackgroundColor, 4, 2))
);

$image = imagecreate($width, $height);
$backgroundColor = imagecolorallocate($image, $bca['r'], $bca['g'], $bca['b']);

if ($text !== null) {

    $textColor = imagecolorallocate($image, $tca['r'], $tca['g'], $tca['b']);
    $textBackgroundColor  = imagecolorallocate($image, $tbca['r'], $tbca['g'], $tbca['b']);

    $font = 5;
    $textBoxWidth = imagefontwidth($font) * mb_strlen($text);
    $textBoxHeight = imagefontheight($font);
    $textPositionX = ($width - $textBoxWidth) / 2;
    $textPositionY = ($height - $textBoxHeight) / 2;
    imagefilledrectangle(
        $image,
        $textPositionX,
        $textPositionY,
        $textPositionX + $textBoxWidth,
        $textPositionY + $textBoxHeight,
        $textBackgroundColor
    );
    imagestring(
        $image,
        $font,
        $textPositionX,
        $textPositionY,
        $text,
        $textColor
    );
}

// -----------------------------------------------------------------------------
// Output image
header('Content-Type: image/png');
switch ($format)
{
    case 'png':
        imagepng($image);
        break;
}
