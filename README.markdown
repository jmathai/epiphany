The Epiphany PHP Framework
=======================
#### Fast. Easy. Clean. RESTful

----------------------------------------

### How it looks

The most basic example is including the routing module and defining a few endpoints and providing a callback function that executes when someone requests that page.

    Epi::init('route');
    getRoute()->get('/', 'home');
    getRoute()->get('/contact', 'contactUs');
    getRoute()->run();
    
    function home() {
        echo 'You are at the home page';
    }

    function contactUs() {
        echo 'Send us an email at <a href="mailto:foo@bar.com">foo@bar.com</a>';
    }

----------------------------------------    

### Learn more about the modules

Read documentation on the individual modules available in Epiphany.

1. [Route][route] - A RESTful routing library to map paths to functions.
2. [Api][api] - An API helper module to create private and public APIs. This is both new and awesome.
3. [Session][session] - A multi-engine session library which supports native PHP sessions, APC and Memcached.
4. [Database][database] - A simple interface to PDO's MySql driver.
5. [Cache][cache] - A easy caching library which supports APC and Memcached.
6. [Config][config] - An ini based configuration library that supports overloading.

----------------------------------------


### The Manifesto

The Epiphany framework is fast, easy, clean and RESTful. The framework does not do a lot of magic under the hood. It is, by design, very simple and very powerful.

The documentation provides a few conventions that we believe lead to well written code but you're free to use any style you'd like. The framework never dictates how you should write or structure your application.

----------------------------------------

### What you need

The Epiphany framework only requires PHP 5+, Apache and mod_rewrite. That's all!

----------------------------------------

### Getting started

The following links to documentation and articles will help you get up and running in no time. Included in the repository is an example directory with sub applications highlighting the different features of the Epiphany framework.

1. <http://github.com/jmathai/epiphany>

### The authors

Get in touch with the authors if you have suggestions or questions.
<table>
  <tr>
    <td><img src="http://www.gravatar.com/avatar/e4d1f099d40e3b453be3355349b90457?s=60"></td><td valign="middle">Jaisen Mathai<br>jaisen-at-jmathai.com<br><a href="http://www.jaisenmathai.com">http://www.jaisenmathai.com</a></td>
  </tr>
  <tr>
    <td><img src="http://www.gravatar.com/avatar/nohash?s=60"></td><td valign="middle">Kevin Hornschemeier<br>khornschemeier-at-gmail.com<br><a href="http://www.khornschemeier.com">http://www.khornschemeier.com</a></td>
  </tr>

</table>


[route]: https://github.com/jmathai/epiphany/blob/master/docs/Route.markdown
[api]: https://github.com/jmathai/epiphany/blob/master/docs/Api.markdown
[session]: https://github.com/jmathai/epiphany/blob/master/docs/Session.markdown
[database]: https://github.com/jmathai/epiphany/blob/master/docs/Database.markdown
[cache]: https://github.com/jmathai/epiphany/blob/master/docs/Cache.markdown
[config]: https://github.com/jmathai/epiphany/blob/master/docs/Config.markdown
