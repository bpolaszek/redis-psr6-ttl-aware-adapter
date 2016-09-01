Description
-----------

Override of Symfony PSR6 cache RedisAdapter, retrieving expiration on an existing item.

This allows, for instance, to increment a value into the cache until it expires.
The original Symfony cache implementation actually resets the expiration delay after each save().

Example use :
----------

```php
$redis = new Redis();
$redis->connect('127.0.0.1');
$cache = new \BenTools\Cache\Adapter\RedisTTLAwareAdapter($redis);

$key = 'my.example';

# Assume my.example does not exist
var_dump($cache->hasItem($key)); // false
$item = $cache->getItem($key);
$item->set(1);
$item->expiresAfter(5); // expires in 5 seconds
$cache->save($item);
sleep(2);

# After 2 seconds, my.example == 1
var_dump($cache->hasItem($key)); // true
$item = $cache->getItem($key);
$value = $item->get(); // 1
var_dump($value); // 1

$item->set($value + 1);
$cache->save($item);
sleep(2);


# After 4 seconds, my.example == 2
var_dump($cache->hasItem($key)); // true
$item = $cache->getItem($key);
$value = $item->get();
var_dump($value); // 2

$item->set($value + 1);
$cache->save($item);
sleep(2);

# After 6 seconds, my.example does not exist anymore
var_dump($cache->hasItem($key)); // false
$item = $cache->getItem($key);
$value = $item->get();
var_dump($value); // null

```

Installation
----------

```
composer require bentools/redis-psr6-ttl-aware-adapter
```