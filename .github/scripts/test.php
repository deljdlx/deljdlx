<?php

class GithubClient
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getOwnRepositories(): array
    {
        $getRepostoryUrl = 'https://api.github.com/user/repos';

        $context = stream_context_create([
            "http" => [
                "header" => "User-Agent: PHP\r\n" .
                    "Authorization: token {$this->token}\r\n" .
                    "Accept: application/vnd.github.v3+json\r\n"
            ]
        ]);
        $json = file_get_contents($getRepostoryUrl, false, $context);
        $repositories = json_decode($json, true);

        $ownRepositories = [];

        foreach ($repositories as $repository) {
            if ($repository['owner']['login'] === 'deljdlx') {
                $ownRepositories[] = $repository;
            }
        }

        return $ownRepositories;
    }
}





$options = getopt('', ['token:']);
$token = $options['token'];

$client = new GithubClient($token);
$ownRepositories = $client->getOwnRepositories();
print_r($ownRepositories);
