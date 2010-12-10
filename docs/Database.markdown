The Epiphany PHP Framework
=======================
#### Fast. Easy. Clean. RESTful

----------------------------------------

### Understanding and using the database module

The database module is a clean interface to PHP's MySql PDO driver. It provides a simple way to use prepared statements to execute queries.

    Epi::init('database');
    EpiDatabase::employ('mysql', 'database', 'host', 'username', 'password');
    $user = getDatabase()->one('SELECT * FROM user WHERE userId=:id', array(':id' => $userId));

First you'll need to include the database module. Then you can specify the type, database name, host, username and password. A connection won't be made to MySql until you issue a query. Here we call the `one` method to get a single entry from the database.

----------------------------------------

### Using the helper function

You can call the `getDatabase` helper function from anywhere in your code to get access to a singleton instance of `EpiDatabase`.

    getDatabase()->all('SELECT * FROM user');

----------------------------------------

### Ways to perform queries

There are three methods to perform queries. They are `one`, `all` and `execute`. The `one` method gets a single record and is useful when you want to query for a single user. The `all` method returns a collection of rows or array of arrays. The `execute` method should be used for INSERT, UPDATE and DELETE statements.

Since the library uses PDO you can write your SQL as a prepared statement with named placeholders denoted by a leading : (colon). The second parameter to all of these methods are an associative array where the key is the placeholder.

    getDatabase()->execute('DELETE FROM user WHERE userId=:id', array(':id' => $userId));

You do not have to worry about escaping user input as long as you use named placeholders because the PDO library will do that for you.

----------------------------------------

### Available methods

In addition to `one`, `all` and `execute` you can call `insertId` to get the id of the last inserted row. The `execute` method returns the last insert id for INSERT statements and the number of affected rows for UPDATE or DELETE statements.

    one($sql, $params);
    all($sql, $params);
    execute($name, $value);
    insertId()

