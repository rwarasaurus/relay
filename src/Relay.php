<?php declare(strict_types=1);

namespace Relay;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class Relay implements RequestHandlerInterface
{
    /**
     * @var array
     */
    protected $queue;

    /**
     * @var callable
     */
    protected $resolver;

    /**
     * @param array
     * @param callable|null
     */
    public function __construct(array $queue, callable $resolver = null)
    {
        if (count($queue) === 0) {
            throw new InvalidArgumentException('$queue cannot be empty');
        }
        $this->queue = $queue;
        reset($this->queue);
        $this->resolver = $resolver ?: function ($entry) {
            return $entry;
        };
    }

    /**
     * @return MiddlewareInterface
     */
    public function nextMiddleware(): MiddlewareInterface
    {
        $entry = current($this->queue);
        $middleware = call_user_func($this->resolver, $entry);
        next($this->queue);
        return $middleware;
    }

    /**
     * @param ServerRequestInterface
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        return $this->nextMiddleware()->process($request, $this);
    }
}
