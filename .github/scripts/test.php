<?php

$getRepostoryUrl = 'https://api.github.com/user/repos';

$options = getopt('', ['token:']);

print_r($options);

$token = $options['token'];

echo strlen($token);
echo "\n";
echo substr($token, 0, 4);
echo '-';
echo substr($token, 4);

$context = stream_context_create([
    "http" => [
        "header" => "User-Agent: PHP\r\n" .
            "Authorization: token $token\r\n" .
            "Accept: application/vnd.github.v3+json\r\n"
    ]
]);
$json = file_get_contents($getRepostoryUrl, false, $context);
$repositories = json_decode($json, true);

print_r($repositories);



