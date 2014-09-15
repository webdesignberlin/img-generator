<?php

$letters = array();
$basicLetters = range('a', 'z');
$letters += $basicLetters;
foreach ($basicLetters as $firstLetter) {

    foreach ($basicLetters as $secondeLetter) {

        $key = $firstLetter . $secondeLetter;
        $letters[] = $key;
    }
}

foreach (range(0, 107) as $number) {

    print($letters[$number]);
    print('<br>');
}

function rS($number, array $sequence) {

    $sequenceCount = count($sequence);
    $digit = floor($sequenceCount / $number);
    foreach (range(0, $number) as $number) {

    }
}
?>
