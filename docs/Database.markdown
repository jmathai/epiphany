Database
=======================
#### The Epiphany PHP Framework

----------------------------------------

### Understanding and using the database module

The database module is a clean interface to PHP's MySql PDO driver. It provides a simple way to use prepared statements to execute queries.

    Epi::init('database');
    EpiDatabase::employ('mysql', 'database', 'host', 'username', 'password');
    $user = getDatabase()->one('SELECT * FROM user WHERE userId=:id', array(':id' => $userId));

First you'll need to include the database module. Then you can specify the type, database name, host, username and password. A connection won't be made to MySql until you issue a query. Here we call the `one` method to get a single entry from the database.

----------------------------------------

### Ways to perform queries

There are three methods to perform queries. They are `one`, `all` and `execute`. The `one` method gets a single record and is useful when you want to query for a single user. The `all` method returns a collection of rows or array of arrays. The `execute` method should be used for INSERT, UPDATE and DELETE statements.

Since the library uses PDO you can write your SQL as a prepared statement with named placeholders denoted by a leading : (colon). The second parameter to all of these methods are an associative array where the key is the placeholder.

    getDatabase()->execute('DELETE FROM user WHERE id=:id', array(':id' => $userId));

You do not have to worry about escaping user input as long as you use named placeholders because the PDO library will do that for you.

### SELECT queries

There are two methods for retrieving data. If you want a single row from the database you should call `one()`. To get multiple rows you should call `all()`.

    $singleRow = getDatabase()->one('SELECT * FROM tbl WHERE id=:id', array(':id' => 1));
    $manyRows = getDatabase()->all('SELECT * FROM tbl WHERE id>:id', array(':id' => 0));

### INSERT, UPDATE and DELETE

To write to the database you'll want to call the `execute()` method.

    $userId = getDatabase()->execute('INSERT INTO user(id, name) VALUES(:id, :name)', array(':id' => 1, ':name' => 'jmathai'));
    $affectedRows = getDatabase()->execute('UPDATE user SET name=:name WHERE id=:id', array(':id' => 1, ':name' => 'Jaisen'));
    $affectedRows = getDatabase()->execute('DELETE FROM user WHERE id=:id', array(':id' => 1));

The return value when you call execute depends on the type of query. For `INSERT` queries on tables with an `auto_increment` column you'll get the `auto_increment` value. For `UPDATE` and `DELETE` queries the return value is the number of affected rows.

----------------------------------------

### Available methods

In addition to `one`, `all` and `execute` you can call `insertId` to get the id of the last inserted row. The `execute` method returns the last insert id for INSERT statements and the number of affected rows for UPDATE or DELETE statements.

    one($sql, $params);
    all($sql, $params);
    execute($name, $value);
    insertId();
