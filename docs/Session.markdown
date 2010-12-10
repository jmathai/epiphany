Session
=======================
#### The Epiphany PHP Framework

----------------------------------------

### Understanding and using the session module

When using the session module you can select between native PHP sessions, APC or Memcached. Simple applications run on a single server can easily use native PHP sessions. More heavily trafficked sites with multiple servers can leverage Memcached.

The interfaces the session engines are identical so you can switch between them with ease.

    Epi::init('session');
    $session = EpiSession::getInstance(EpiSession::PHP);
    $session->set('name', 'value');
    $session->get('name');

First you'll need to include the session module and specify which engine you'd like to use. You can get a singleton instance of the caching object by calling `EpiSession::getInstance()` which takes either `EpiSession::PHP`, `EpiSession::APC` or `EpiSession::MEMCACHED` as a parameter.

----------------------------------------

### Using the helper function

You can call the `getSession` helper function from anywhere in your code to get access to a singleton instance of `EpiSession`. The default caching engine is PHP but you can override this by calling `EpiSession::employ`.

    EpiSession::employ(EpiSession::MEMCACHED);
    getSession()->get('name');

----------------------------------------

### Available methods

The available methods are `get`, `set` and `end`.

    get($name);
    set($name, $value);
    delete($name);

----------------------------------------

### Requirement for using Memcached

PHP has two different Memcached classes. Epiphany requires the `Memcached` class. The similarly named `Memcache` class does not have a driver in the Epiphany library. To see the differences you can view the respective manual pages.

The `Memcached` class (http://php.net/Memcached) is supported but the `Memcache` class (http://php.net/Memcache) is not. You can check if you have the approprate classes available like this.

    if(class_exists('Memcached')) {
        echo 'You have got the required Memcached class.';
    } else {
        echo 'You do NOT have the required Memcached class.';
    }

