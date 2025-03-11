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

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\RpcClient\ServiceClient;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[Aspect]
class RPCAspect extends AbstractAspect
{
    public array $classes = [
        ServiceClient::class . '::__call',
    ];

    public function __construct(protected EventDispatcherInterface $dispatcher)
    {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $beginTime = microtime(true);
        $throwable = null;
        $result = null;

        try {
            $result = $proceedingJoinPoint->process();
        } catch (Throwable $ex) {
            $throwable = $ex;
        }

        try {
            $method = $proceedingJoinPoint->arguments['keys']['method'] ?? '';
            $params = $proceedingJoinPoint->arguments['keys']['params'] ?? [];
            $service = $proceedingJoinPoint->getInstance()->getServiceName();

            $time = microtime(true) - $beginTime;

            $this->dispatcher->dispatch(new RPCEvent($service, $method, $params, $result, $time, $throwable));
        } catch (Throwable) {
        }

        if (! is_null($throwable)) {
            throw $throwable;
        }

        return $result;
    }
}
