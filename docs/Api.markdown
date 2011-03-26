Api
=======================
#### The Epiphany PHP Framework

----------------------------------------

### Understanding the Api module

You can see an [example][example] in the `examples` directory. This module depends on [Route][route] and supports regular expressions in the same way.

If you've ever wanted to create an API and use it to build your application then read on. The Api module makes it easy to define, expose and consume an API for your application.

    Epi::init('api');
    getApi()->get('/version.json', array('Api', 'version'), EpiApi::external);
    getApi()->get('/users.json', array('Api', 'users'), EpiApi::external);
    getRoute()->get('/users', array('Site', 'users'));
    getRoute()->run();

The first thing we need to do is include the api module using `Epi::init`. After that we can begin defining our API routes. We define a route to return the API version and another to list all our users. Finally we call `getRoute()->run()` method which sets up the routes. Public routes will be accessible via HTTP/PHP while private routes are accessible only using PHP.

----------------------------------------

### Creating an API for internal consumption

Since you're already in a PHP context you'll want to access your API natively through PHP. Epiphany makes it easy to call your API endpoints directly without having to make an HTTP call.

In this example we'll continue from the example we started above and define the `version` and `users` methods. We'll then use that API to create a webpage that renders users in an ordered list.

    class Api {
      public static function version() {
        return '1.0';
      }

      public static function users() {
        $users = array(
          array('username' => 'jmathai'),
          array('username' => 'stevejobs'),
          array('username' => 'billgates')
        );
        return $users;
      }
    }

Next we can define the class method to display a list of users. From PHP we can natively call our API to get the list of users by using `invoke()`.

    class Site {
      public static function users()
      {
        $users = getApi()->invoke('/users.json');
        echo '<ul>';
        foreach($users as $user) {
          echo "<li>{$user['username']}</li>"
        }
        echo '</ul>';
      }
    }

### Accessing your API externally over HTTP

If you access `/version.json` then you'll get the results from `Api::version` and similar for `/users.json`.

    curl http://localhost/version.json
    "1.0"

    curl http://localhost/users.json
    [{"username":"jmathai"},{"username":"stevejobs"},{"username":"billgates"}]

Without having to add any code you have a RESTful HTTP API.

----------------------------------------

### Permissioning your API endoints

The purpose of the `api` module is to enable you to build your application on top of a defined API. Obviously you won't want all of your API endpoints to be publicly available over HTTP. For this reason you'll want to specify the visibility on your routes.

For security purposes the visibility defaults to private. However, you can specify this when you define the route. If we wanted our `/version.json` endpoint to remain public while making `/users.json` private we'd define the routes as below.

    getApi()->get('/version.json', array('Api', 'version'), EpiApi::external);
    getApi()->get('/users.json', array('Api', 'users'), EpiApi::internal);

Since API endpoints are private by default you do not have to specify `EpiApi::internal`.

----------------------------------------

### Loading your API routes from an external file

Not yet implemented.

[route]: https://github.com/jmathai/epiphany/blob/master/docs/Route.markdown
[example]: https://github.com/jmathai/epiphany/blob/master/examples/api/index.php
