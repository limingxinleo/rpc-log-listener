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

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\Codec\Json;
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

    public function process(object $event)
    {
        if ($event instanceof RPCEvent) {
            if ($event->throwable) {
                $this->logger->error($this->format($event));
                return;
            }

            if ($event->time > 0.2) {
                // 内部RPC请求，超过200ms时，记录日志
                $this->logger->error($this->format($event));
            } else {
                $this->logger->info($this->format($event));
            }
        }
    }

    protected function format(RPCEvent $event): string
    {
        $result = $event->throwable ? ['rpc_failed' => true, 'message' => $event->throwable->getMessage()] : $event->result;
        return Json::encode([
            'service' => $event->service,
            'method' => $event->method,
            'params' => $event->params,
            'result' => $result,
            'time' => $event->time,
        ]);
    }
}
