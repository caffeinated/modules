<?php

namespace Caffeinated\Modules\Repositories;

use App\Libraries\Str;
use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Redis;

class RedisRepository extends Repository
{

    /**
     * Unique key to save optimized module definition in Redis.
     * The key is made from the application name + application
     * encryption key + the string "module.json". Each part
     * is separated by the underscore character.
     *
     * @var string
     */
    protected $key;

    /**
     * RedisRepository constructor. Calls the parent constructor
     * and set the unique key to use with redis.
     *
     * @param Config $config
     * @param Filesystem $files
     */
    public function __construct(Config $config, Filesystem $files)
    {
        parent::__construct($config, $files);
        $this->key = config('app.name') . '_' . config('app.key') . '_module.json';
    }

    /**
     * Returns the entire module repository content as a collection.
     *
     * @return Collection
     */
    public function load()
    {
        $value = Redis::get($this->key);
        if (Str::isNullOrEmptyString($value)) {
            Redis::set($this->key, json_encode([], JSON_PRETTY_PRINT));
            $this->optimize();
            $value = Redis::get($this->key);
        }
        return collect(json_decode($value, true));
    }

    /**
     * Saves the content to the repository.
     *
     * @param $content
     * @return int|bool a non-zero or true value on success, or false on failure.
     */
    public function save($content)
    {
        return Redis::set($this->key, $content);
    }

}
