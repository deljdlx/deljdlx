<?php

$getRepostoryUrl = 'https://api.github.com/user/repos';

$options = getopt('', ['token:']);
$token = $options['token'];



//curl -H "Authorization: token YOUR_PERSONAL_ACCESS_TOKEN" \
//     "https://api.github.com/user/repos?visibility=all&affiliation=owner&per_page=100"


$context = stream_context_create([
    "http" => [
        "header" => "User-Agent: PHP\n" .
            "Authorization: token $token\n"
    ]
]);
$json = file_get_contents($getRepostoryUrl, false, $context);
$repositories = json_decode($json, true);

print_r($repositories);



