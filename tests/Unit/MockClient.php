<?php

namespace CodeOfDigital\LaravelUrlShortener\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Utils;
use Psr\Http\Message\ResponseInterface;

class MockClient extends Client
{
    protected $handler;
    protected $history;

    public function __construct(array $config = [])
    {
        $this->handler = new MockHandler();
        $this->history = [];

        parent::__construct($config + ['handler' => $this->newHandlerStack($this->handler)]);
    }

    /**
     * Get the client history
     *
     * @param int|null $at
     * @return array|mixed|null
     */
    public function getHistory(int $at = null)
    {
        if (is_null($at)) {
            return $this->history;
        }

        return $this->history[$at] ?? null;
    }

    /**
     * Get a previously made request
     *
     * @param int $at
     * @return mixed|null
     */
    public function getRequest(int $at)
    {
        return $this->getHistory($at)['request'] ?? null;
    }

    /**
     * Get the amount of messages waiting in the queue.
     *
     * @return int
     */
    public function getQueueSize()
    {
        return $this->handler->count();
    }

    /**
     * Determine if there are queued messages.
     *
     * @return bool
     */
    public function hasQueuedMessages()
    {
        return $this->getQueueSize() > 0;
    }

    /**
     * Get a fresh handler stack
     *
     * @param null $handler
     * @return HandlerStack
     */
    protected function newHandlerStack($handler = null)
    {
        $stack = new HandlerStack($handler ?: Utils::chooseHandler());
        $stack->push(Middleware::history($this->history));
        $stack->push(Middleware::httpErrors());
        return $stack;
    }

    /**
     * Queue the given response
     *
     * @param ResponseInterface ...$responses
     * @return $this
     */
    public function queue(ResponseInterface ...$responses)
    {
        $this->handler->append(...$responses);

        return $this;
    }
}
