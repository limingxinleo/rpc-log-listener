# RPC Log Listener for Hyperf

[![PHPUnit](https://github.com/limingxinleo/rpc-log-listener/actions/workflows/test.yml/badge.svg)](https://github.com/limingxinleo/rpc-log-listener/actions/workflows/test.yml)

```
composer require hyperf/rpc-log-listener
```

## How to use

修改配置 `config/autoload/listeners.php` 增加以下监听器。

```php
return [
    Hyperf\RPCLogListener\RPCEventListener::class,
];
```

当然，你也可以根据 `Hyperf\RPCLogListener\RPCEventListener` 来设计适配自己的监听器。
