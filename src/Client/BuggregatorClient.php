<?php

declare(strict_types=1);

namespace MarekSkopal\BuggregatorClient\Client;

use DateTimeImmutable;
use Http\Discovery\Psr18Client;
use Psr\Http\Message\ResponseInterface;

class BuggregatorClient
{
    public function __construct(private readonly string $appName, private readonly string $url, private readonly DateTimeImmutable $date)
    {
    }

    /** @param array<mixed> $data */
    public function sendRequest(array $data): ResponseInterface
    {
        $client = new Psr18Client();

        $body = [
            'profile' => $data,
            'tags' => [],
            'app_name' => $this->appName,
            'hostname' => gethostname(),
            'date' => $this->date->getTimestamp(),
        ];

        $request = $client->createServerRequest('POST', $this->url);
        $request->withBody($client->createStream((string) json_encode($body)));

        return $client->sendRequest($request);
    }
}
