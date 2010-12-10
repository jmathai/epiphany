The Epiphany PHP Framework
=======================
#### Fast. Easy. Clean. RESTful

----------------------------------------

### Understanding and using the cache module

When using the cache module you can select between APC or Memcached. The interface to both are identical so you can switch between them at anytime and in different environments.

    Epi::init('cache');
    $cache = EpiCache::getInstance(EpiCache::APC);
    $cache->set('name', 'value');
    $cache->get('name');

First you'll need to include the cache module and specify which caching engine you'd like to use. You can get a singleton instance of the caching object by calling `EpiCache::getInstance()` which takes either `EpiCache::APC` or `EpiCache::MEMCACHED` as a parameter.

----------------------------------------

### Using the helper function

You can call the `getCache` helper function from anywhere in your code to get access to a singleton instance of `EpiCache`. The default caching engine is APC but you can override this by calling `EpiCache::employ`.

    EpiCache::employ(EpiCache::MEMCACHED);
    getCache()->get('name');

----------------------------------------

### Available methods

The available methods are `get`, `set` and `delete`.

    get($name);
    set($name, $value[, $ttl]);
    delete($name);

The default value for `$ttl` is 0 which means it will be stored forever. For the Memcached engine the `$ttl` can be seconds from the current time as long as it is less than `60*60*24*30` (seconds in 30 days) otherwise it needs to be a Unix timestamp.



----------------------------------------

### Requirement for using Memcached

PHP has two different Memcached classes. Epiphany requires the `Memcached` class. The similarly named `Memcache` class does not have a driver in the Epiphany library. To see the differences you can view the respective manual pages.

The `Memcached` class (http://php.net/Memcached) is supported but the `Memcache` class (http://php.net/Memcache) is not. You can check if you have the approprate classes available like this.

    if(class_exists('Memcached')) {
        echo 'You have got the required Memcached class.';
    } else {
        echo 'You do NOT have the required Memcached class.';
    }

