<?php

require __DIR__ . '/vendor/autoload.php';

$first = new class implements Psr\Http\Server\MiddlewareInterface {
    public function process(Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Server\RequestHandlerInterface $handler): Psr\Http\Message\ResponseInterface {
        $response = $handler->handle($request);
        $response->getBody()->write('1');
        return $response;
    }
};

$second = new class implements Psr\Http\Server\MiddlewareInterface {
    public function process(Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Server\RequestHandlerInterface $handler): Psr\Http\Message\ResponseInterface {
        $response = $handler->handle($request);
        $response->getBody()->write('2');
        return $response;
    }
};

$thrid = new class implements Psr\Http\Server\MiddlewareInterface {
    public function process(Psr\Http\Message\ServerRequestInterface $request, Psr\Http\Server\RequestHandlerInterface $handler): Psr\Http\Message\ResponseInterface {
        $psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
        $body = $psr17Factory->createStream('3');
        return $psr17Factory->createResponse(200)->withBody($body);
    }
};

$psr17Factory = new \Nyholm\Psr7\Factory\Psr17Factory();
$request = $psr17Factory->createServerRequest('GET', 'http://tnyholm.se');

$relay = new Relay\Relay([$first, $second, $thrid]);
$response = $relay->handle($request);

var_dump((string) $response->getBody());
