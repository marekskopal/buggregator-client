<?php

declare(strict_types=1);

namespace MarekSkopal\BuggregatorClient\Middleware;

use DateTimeImmutable;
use MarekSkopal\BuggregatorClient\Client\BuggregatorClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function xhprof_disable;
use function xhprof_enable;

class XhprofMiddleware implements MiddlewareInterface
{
    /** @param array<string> $ignoredFunctions */
    public function __construct(
        private readonly string $appName,
        private readonly string $url,
        private readonly int $flags = XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_CPU | XHPROF_FLAGS_NO_BUILTINS,
        private readonly array $ignoredFunctions = [],
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $startDate = new DateTimeImmutable();

        $this->start();

        $response = $handler->handle($request);

        $this->end($startDate);

        return $response;
    }

    private function start(): void
    {
        if (!function_exists('xhprof_enable')) {
            return;
        }

        $ignoredFunctions = [
            'xhprof_disable',
            'MarekSkopal\BuggregatorClient\Middleware\XhprofMiddleware::end',
        ];

        $options = [
            'ignored_functions' => array_merge($ignoredFunctions, $this->ignoredFunctions),
        ];

        xhprof_enable($this->flags, $options);
    }

    private function end(DateTimeImmutable $date): void
    {
        if (!function_exists('xhprof_disable')) {
            return;
        }

        $data = xhprof_disable();

        $buggregatorClient = new BuggregatorClient($this->appName, $this->url, $date);
        $buggregatorClient->sendRequest($data);
    }
}
