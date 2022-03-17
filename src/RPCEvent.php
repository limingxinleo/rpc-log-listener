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

use Throwable;

class RPCEvent
{
    public function __construct(
        public string $service,
        public string $method,
        public array $params,
        public mixed $result,
        public float $time,
        public ?Throwable $throwable
    ) {
    }
}
