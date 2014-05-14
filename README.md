### crunchDB - Yet another *json* database?
Yes, crunchDB is a simple database system written in PHP that is based on storing information in multiple json files. Each json file represents a table and can be accessed and modified via crunchDB.
Well, there already are other json based systems, so why creating another one? Actually, though there are a lot of good systems like this out there, I haven't really found anything that offered all the features I wanted in a nice and clear way. Though crunchDB is very simple at the moment, It might get additional functionality to make it more powerful. In it's current state, it is awesome to use for smaller projects like a little chat client. - That's what I wrote it for.

### Project structure
This repository contains a `src` directory which contains all the chrunchDB files. However, if you just want to include chrunchDB and get rolling, I recommend using the `crunchdb.lib.php` file which contains all classes in a single file. Also, there is an example file to demonstrate some actions. You can download and run this file, it will delete all the changes it made by dropping its created tables after testing.

### Features
So, obviously, crunchDB needs to have some sort of functionality. It would be pretty useless otherwise, wouldn't it?  
Here's a list of all functions that are currently implemented.

`$cdb = new crunchDB('./db/', 'json', 'rw')` Initialize crunchdb with the following parameters:  
**directory** - The path to the directory where all the json files will be stored in.  
**extension** - The extension of the db files. (This parameter is optional, it is set to `json` as default)  
**db mode** - The database access mode. Can be read(r), write(w) or (rw).  (This parameter is optional, it is set to `rw` as default)

| Method | Description |
| --- | --- |
| `crunchDB -> version()` | Return the current crunchDB version. Useful to see if everything is set up|
| `crunchDB -> create('tablename')` | Add a new table (eq. a new JSON file) with the given name. |
| `crunchDB -> drop('tablename')` | Drop the table with the given name and delete its JSON file.|
| `crunchDB -> alter('tablename', 'newname)` | Rename the table called `tablename` to `newname`.|
| `crunchDB -> insert('tablename', array())` | Insert a row with the information stored in the `data` array into the table `tablename`.|
| `crunchDB -> count('tablename', 'key', 'value')` | Count all entries in the table `tablename` where `key` matches `value`. You can use `count('tablename', '*')` to count **all** entries in this table.|
| `crunchDB -> select('tablename', 'key', 'value')` | Select all entries in the table `tablename` where `key` matches `value`. You can use `count('tablename', '*')` to select **all** entries in this table.|
| `crunchDB -> update('tablename', 'key', 'value', array('key' => 'value')` | Update all entries in the table `tablename` where `key` matches `value`. All fields that are set in the array will be updated. |
| `crunchDB -> updateAll('tablename', 'key', 'value', array('key' => 'value')` | Update all entries in the table `tablename` where `key` matches `value`. The full row will be replaced with the given array. |
| `crunchDB -> delete('tablename', 'key', 'value')` | Deleteall entries in the table `tablename` where `key` matches `value`. You can use `count('tablename', '*')` to delete **all** entries in this table (=truncate).|
