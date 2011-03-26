Session
=======================
#### The Epiphany PHP Framework

----------------------------------------

### Understanding and using the session module

When using the session module you can select between native PHP sessions, APC or Memcached. Simple applications run on a single server can easily use native PHP sessions. More heavily trafficked sites with multiple servers can leverage Memcached.

The interfaces the session engines are identical so you can switch between them with ease.

    Epi::init('session');
    EpiSession::employ(EpiSession::PHP);
    getSession()->set('name', 'value');
    getSession()->get('name');

First you'll need to include the session module and specify which engine you'd like to use. You can get a singleton instance of the caching object by calling `EpiSession::getInstance()` which takes either `EpiSession::PHP`, `EpiSession::APC` or `EpiSession::MEMCACHED` as a parameter.

### Selecting a session driver

To specify which session driver to use you should pass the appropriate value to `EpiSession::employ()`. The interfaces are identical so you can switch between them without issues.

    EpiSession::PHP
    EpiSession::APC
    EpiSession::Memcached

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

