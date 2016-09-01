<?php

namespace BenTools\Cache\Adapter;

use Predis\Connection\Factory;
use Predis\Connection\Aggregate\PredisCluster;
use Predis\Connection\Aggregate\RedisCluster;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Exception\InvalidArgumentException;

class RedisTTLAwareAdapter extends RedisAdapter
{
    private $redis;

    /**
     * @param \Redis|\RedisArray|\RedisCluster|\Predis\Client $redisClient
     */
    public function __construct($redisClient, $namespace = '', $defaultLifetime = 0)
    {
        parent::__construct($redisClient, $namespace, $defaultLifetime);

        if (preg_match('#[^-+_.A-Za-z0-9]#', $namespace, $match)) {
            throw new InvalidArgumentException(sprintf('RedisAdapter namespace contains "%s" but only characters in [-+_.A-Za-z0-9] are allowed.', $match[0]));
        }
        if (!$redisClient instanceof \Redis && !$redisClient instanceof \RedisArray && !$redisClient instanceof \RedisCluster && !$redisClient instanceof \Predis\Client) {
            throw new InvalidArgumentException(sprintf('%s() expects parameter 1 to be Redis, RedisArray, RedisCluster or Predis\Client, %s given', __METHOD__, is_object($redisClient) ? get_class($redisClient) : gettype($redisClient)));
        }
        $this->redis = $redisClient;
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        $item = parent::getItem($key);
        $ttl  = $this->redis->ttl($key);
        if ($ttl > 0) {
            $item->expiresAfter($ttl);
        }
        return $item;
    }


}
