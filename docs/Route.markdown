Route
=======================
#### The Epiphany PHP Framework

----------------------------------------

### Understanding and using routes

Routes represent pages of your application. Traditionally you would have a file like contact.php that resided in your web root. Routes act the same way but provide a lot more functionality. Instead of having a physical file for every single URL of your application you can define them programmatically. You could define a route as `/contactus` and provide a class method or function. Here's an example.

    Epi::init('route');
    getRoute()->get('/', 'home');
    getRoute()->get('/contactus', 'contactUs');
    getRoute()->post('/contactus', 'contactUsPost');
    getRoute()->run();

The first thing we need to do is include the route module using `Epi::init`. Then we create an instance of `EpiRoute`. After that we can begin defining our routes. We define a route for our home page and another for a contact page. Each of list a function which gets called when a user visits the corresponding page. Finally we call the `run` method to execute the page and call the appropriate callback function.

Notice how we specified the `/contactus` route twice with different methods. This allows you to specify different functions to handle the request based on HTTP method.

----------------------------------------

### Configuring Apache using .htaccess or VirtualHost directive

In order for the routing to function you'll need to have `mod_rewrite` installed. You can specify the following inside of your VirtualHost directive or in a .htaccess file inside your web root.

    RewriteEngine on
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)\?*$ index.php?__route__=/$1 [L,QSA]
	
### Configuring Nginx

In order for the routing to function you'll need to have `HttpRewriteModule` installed. You can specify the following inside of your server configuration.

	if (!-e $request_filename) {
	  rewrite ^(.*) /index.php?__route__=$1 last;
	}

### Configuring IIS using Web.config file

In order for the routing to function you'll need to have `URL Rewrite Module` installed. You can specify the following inside of your Web.config file inside your web root.

    <?xml version="1.0" encoding="UTF-8"?>
    <configuration>
        <system.webServer>
            <rewrite>
                <rules>
                    <rule name="epiphany" patternSyntax="Wildcard">
                        <match url="*" />
                        <conditions>
                            <add input="{REQUEST_FILENAME}" matchType="IsFile" negate="true" />
                            <add input="{REQUEST_FILENAME}" matchType="IsDirectory" negate="true" />
                        </conditions>
                        <action type="Rewrite" url="index.php?__route__=/{R:1}" appendQueryString="true" />
                    </rule>
                </rules>
            </rewrite>
        </system.webServer>
    </configuration>

----------------------------------------

### Using class methods instead of functions

If your application has more than 15 routes then you'll want to consider using controller classes to help organize your code. Let's say that your application consists of user facing HTML pages and an API for developers. You could separate code for each into different classes by creating a `Site` class for the HTML pages and an `Api` class for the API. After including the route module and instantiating an `EpiRoute` object you could do something like this.

    getRoute()->get('/', array('Site', 'home'));
    getRoute()->get('/api/profile', array('Api', 'profile'));
    getRoute()->run();

When using class methods you should define them as `public` and `static`. This enables the route module to easily call them.

----------------------------------------

### Using regular expressions in your routes

Hard coded routes work fine for many pages but often times you'll want to have dynamic paths. You may, for example, want to include the user id as part of a route. Let's say that you have two routes that looks like `/profile/:id` and `/profile/:id/photos`. This is an example where you want to use regular expressions to define your routes. Your routes would look like this.

    getRoute()->get('/profile/(\d+)', array('Site', 'profile'));
    getRoute()->get('/profile/(\d+)/photos', array('Site', 'photos'));
    class Site {
        public static function profile($userId) {
            // logic for profile page with access to user id from URL
        }

        public static function photos($userId) {
            // logic for photo page with access to user id from URL
        }
    }

Your regular expressions can be as complex as you'd like. Subpatterns denoted by parenthesis are passed into the function or method as positional parameters.

_Note: All routes are regular expressions using # as the delimiters. If you include characters reserved by regular expressions you will need to escape them with a \ (backslash)._

----------------------------------------

### Loading your routes from an external file

In many cases it is more convenient to specify your routes in an ini file and load them into your application. Each section represents a route and they must have unique names. If two routes have the same name the later will overwrite the former. Your ini file should use the following format.

    # specify a class method
    [home]
    method = GET
    path = "/"
    class = Site
    function = home
    
    # specify a function
    [child-new]
    method = POST
    path = "/profile/?(\d+)"
    function = saveProfile

To load your ini file you'll need to set the path to your config file and call the `load` method.

    Epi::setPath('config', '/path/to/config/directory');
    getRoute()->load('filename.ini');

----------------------------------------

### Server side redirects

You can perform server side redirects using this module. The `redirect()` method takes between 1 and 3 parameters.

    getRoute()->redirect('/login'); // does a standard http redirect - defaults to 302
    getRoute()->redirect('/login', 301); // does a 301 redirect
    getRoute()->redirect($someUrl, null, true); // when true, the third parameter allows urls starting with this regex https?:// - defaults to false
    
----------------------------------------

### Compatibility

Another way to set routes that may be more compatible when working with other libraries is

    Epi::init('route');
    $router = new EpiRoute();
    $router->get('/', 'home');
    $router->get('/contactus', 'contactUs');
    $router->post('/contactus', 'contactUsPost');
    $router->run();

That's enough to get started. Have a look at the examples directory for more usages of the route module.
