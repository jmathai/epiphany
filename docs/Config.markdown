Config
=======================
#### The Epiphany PHP Framework

----------------------------------------

### Understanding and using the config module

You can easily use configuration values by using the `get` and `set` methods.

    Epi::init('config');
    getConfig()->set('dbhost', 'localhost');
    getConfig()->get('dbhost');

First you'll need to include the config module. You can get a singleton instance of the config object by calling `EpiConfig::getInstance()`.

----------------------------------------

### Using the helper function

You can call the `getConfig` helper function from anywhere in your code to get access to a singleton instance of `EpiConfig`.

    getConfig()->get('name');

----------------------------------------

### Using ini files for configuration and over loading

You can use an ini file to store your configuration and load those into the configuration module. The module supports nesting one level deep and returns nested nodes as an object.

    dbhost = localhost
    dbuser = root
    dbpass = mypassword
    dbname = mydb

    [paths]
    base = "/www/mysite.com"
    css = "/www/mysite.com/html/css"

    Epi::setPath('config', '/path/to/config/directory');
    $dbhost = getConfig()->get('dbhost');
    $basePath = getConfig()->get('paths')->base

There will be times where you'll want to overload ini files. Let's say you have a default set of values across all environments and some values which change in each one. You could have a default.ini with shared configurations and then separate ini files that overload the values in default.ini.  It's important to call `Epi::setPath('config', '/path/to/config/directory')` so that the library knows where to look for config files.

    getConfig()->load('default.ini', 'overload-1.ini');
    getConfig()->load('overload-2.ini');

In this example the values from default.ini will be overloaded by overload-1.ini which will then be overloaded by overload-2.ini.

----------------------------------------

### Available methods

The available methods are `get`, `set` and `load`.

    get($name);
    set($name, $value);
    load($filename);
