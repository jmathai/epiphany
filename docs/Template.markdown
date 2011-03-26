Template
=======================
#### The Epiphany PHP Framework

----------------------------------------

### Understanding and using the template module

The template module is a lightweight and powerful templating engine that uses native PHP code in the views. Following the best practices in this documentation will keep your views sane.

    Epi::init('template');
    Epi::setPath('view', '/path/to/views/directory');
    getTemplate()->display('template.php', array('name' => 'Jaisen'));


    <!-- template.php -->
    <h1>Hello, <?php echo $name; ?></h1>

First you'll need to include the template module and create an instance of it. You can call the `display` method which takes a template and array of parameters as it's arguments. The associative array becomes available as named variables which you can access inside PHP delimeters.

----------------------------------------

### Keeping your templates clean and logic free

A best practice in your views is to never have a block of php code span more than a single line. Frequent usage of PHP tags is encouraged and helps keep extraneous logic from creeping in.

    <h1>Hello, <?php echo $name; ?></h1>

    <ol>
        <?php foreach($friends as $friend) { ?>
            <li><?php echo $friend['name']; ?></li>
        <?php } ?>
    </ol>

----------------------------------------

### Available methods

The available methods are `display`, `get`, `json` and `jsonResponse`. `get` is identical to `display` but it returns the rendered template as a string. `json` takes any variable and returns a json encoded string. `jsonResponse` does the same as `json` but it adds the json encoded string to the header in an application/x-json header which is used by some AJAX libraries. `jsonResponse` also writes the json to the screen and does not return anything. 

    display('template.php', $params);
    get('template.php', $params);
    json($variable);
    jsonResponse($variable);

