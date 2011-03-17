Api
=======================
#### The Epiphany PHP Framework

----------------------------------------

### Understanding the Api module

This module depends on [Route][route] and supports regular expressions in the same way.

The Api module makes it easy to define, expose and consume an API for your application. If you've ever wanted to create an API and use it to build your application then read on.

    Epi::init('api');
    Epi::setSetting('apiPrefix', '/api');
    $api new EpiApi();
    $api->get('/users', array('Api', 'users'), EpiApi::PUBLIC);
    $api->run();

The first thing we need to do is include the api module using `Epi::init`. Optionally, we can define a prefix for all API endpoints - we use `/api`. Then we create an instance of `EpiApi`. After that we can begin defining our API routes. We define a route to list all our users. Finally we call the `run` method which sets up the routes. Public routes will be accessible via HTTP and PHP while private routes are accessible only using PHP.

----------------------------------------

### How to define an API and consume it internally

Since you're already in a PHP context you'll want to access your API natively through PHP. Epiphany makes it easy to call your API endpoints directly without having to invoke an HTTP call.

In this example we'll create an API to return a list of users. We'll then use that API to create a webpage that renders users in an ordered list.


To create the API just use the code above.

If you access `/api/users` then you'll get the results from `Api::users`. This is because we specified that as the second parameter to the `get` method.

    class Api {
      public static function users() {
        // $users = User::getAllUsers();
        $users = array(
          array('username' => 'jmathai'),
          array('username' => 'johndoe')
        );
        return $users;
      }
    }

That was easy. Now we want to create a webpage that returns an ordered list of users. We do this like any other page and use the `[Route][route]` module which is included when you do `Epi::init('api')`. I'll use the `getApi()` helper function to get a singleton instance of the `EpiApi` object.

    $router = new EpiRoute();
    $router->get('/users', array('Site', 'users'));
    $router->run();

    class Site {
      public static function users() {
        $users = getApi()->invoke('/users');
        getTemplate()->display('users.php', array('users' => $users));
      }
    }

Inside of the `Site::users` method we access the API results by calling `invoke()`. Calling `invoke()` will execute the API internally and give you the results. The users.php template could be as simple as the following code.

    <ul>
      <?php foreach($users as $user) { ?>
        <li><?php echo $user['username']; ?></li>
      <?php } ?>
    </ul>

----------------------------------------

### Permissioning your API endoints

The purpose of the `Api` module is to allow you tu use it exclusively for data access. Obviously you won't want all of your API endpoints to be publicly available over HTTP. For this reason you'll want to specify the visibility on your routes. For security purposes the visibility defaults to private.

----------------------------------------

### Using the helper function

You can call the `getApi` helper function from anywhere in your code to get access to a singleton instance of `EpiApi`.

    getApi()->get('/user', array('Api, 'userShow'));
    getApi()->post('/user', array('Api', 'userAdd'));
    getApi()->run();

----------------------------------------

### Using class methods instead of functions

If your application has more than 15 routes then you'll want to consider using controller classes to help organize your code. Let's say that your application consists of user facing HTML pages and an API for developers. You could separate code for each into different classes by creating a `Site` class for the HTML pages and an `Api` class for the API. After including the route module and instantiating an `EpiRoute` object you could do something like this.

    getRoute()->get('/', array('Site', 'home'));
    getRoute()->get('/api/profile', array('Api', 'profile'));
    getRoute()->run();

When using class methods you should define them as `public` and `static`. This enables the route module to easily call them.

----------------------------------------

### Loading your API routes from an external file

In many cases it is more convenient to specify your routes in an ini file and load them into your application. Each section represents a route and they must have unique names. If two routes have the same name the later will overwrite the former. Your ini file should use the following format.

    # specify a class method
    [user]
    method = GET
    path = "/user/(\d+)"
    class = Api
    function = user
    scope = public
    
    [user-new]
    method = POST
    path = "/user"
    class = Api
    function = userAdd
    scope = private

To load your ini file you'll need to set the path to your config file and call the `load` method.

    Epi::setPath('config', '/path/to/config/directory');
    getRoute()->load('filename.ini');

That's enough to get started. Have a look at the examples directory for more usages of the route module.

[route]: https://github.com/jmathai/epiphany/blob/master/docs/Route.markdown
