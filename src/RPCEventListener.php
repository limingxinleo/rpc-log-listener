<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Hyperf\RPCLogListener;

use Hyperf\Codec\Json;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class RPCEventListener implements ListenerInterface
{
    protected LoggerInterface $logger;

    public function __construct(LoggerFactory $factory)
    {
        $this->logger = $factory->get('rpc');
    }

    public function listen(): array
    {
        return [
            RPCEvent::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof RPCEvent) {
            if ($event->throwable) {
                $this->logger->warning($this->format($event));
                return;
            }

            if ($event->time > $this->expiredTimestamp($event)) {
                // 内部RPC请求，超过200ms时，记录日志
                $this->logger->error($this->format($event));
            } else {
                $this->logger->info($this->format($event));
            }
        }
    }

    public function expiredTimestamp(RPCEvent $event): float
    {
        return 0.2;
    }

    protected function format(RPCEvent $event): string
    {
        $result = $event->result;
        if ($throwable = $event->throwable) {
            $result = [
                'rpc_failed' => true,
                'code' => $throwable->getCode(),
                'message' => $throwable->getMessage(),
            ];
        }

        return Json::encode([
            'service' => $event->service,
            'method' => $event->method,
            'params' => $event->params,
            'result' => $result,
            'time' => $event->time,
        ]);
    }
}
