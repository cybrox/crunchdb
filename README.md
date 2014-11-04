### crunchDB - Yet another *json* database?
Yes, crunchDB is a simple database system written in PHP that is based on storing information in multiple json files. Each json file represents a table and can be accessed and modified via crunchDB.
Well, there already are other json based systems, so why creating another one? Actually, though there are a lot of good systems like this out there, I haven't really found anything that offered all the features I wanted in a nice and clear way. Though crunchDB is very simple at the moment, It might get additional functionality to make it more powerful. In it's current state, it is awesome to use for smaller projects like a little chat client. - That's what I wrote it for.

### Project structure
This repository contains a `src` directory which contains all the chrunchDB files. Also, there is an example file to demonstrate some actions. You can download and run this file, it will delete all the changes it made by dropping its created tables after testing.

### Features
So, obviously, crunchDB needs to have some sort of functionality. It would be pretty useless otherwise, wouldn't it?  
Here's a list of all functions that are currently implemented.

`$cdb = new crunchDB('./db/', 'json', 'rw')` Initialize crunchdb with the following parameters:  
**directory** - The path to the directory where all the json files will be stored in.  
**extension** - The extension of the db files. (This parameter is optional, it is set to `json` as default)  
**db mode** - The database access mode. Can be read(r) or (rw).  (This parameter is optional, it is set to `rw` as default, **It is not yet implemented!**)

| Method | Description |
| --- | --- |
| `crunchDB->version()` | Return the current crunchDB version. Useful to see if everything is set up. |
| `crunchDB->tables()` | List all tables (=files) that are present in this database (=directory). |
| `crunchDB->table('name')` | Get the object of a crunchDB table `cdbTable`. |
| -- | -- | 
| `cdbTable->truncate()` | Truncate the given table object.|
| `cdbTable->drop()` | Drop the given table object and delete its JSON file.|
| `cdbTable->alter('name')` | Rename the table object's file and table to `name`.|
| `cdbTable->raw()` | Get the raw json data of the table's content.|
| `cdbTable->count()` | Count all rows in this table. This is equal to `->select('*')->count()`.|
| `cdbTable->insert('data')` | Insert an array of data, crunchDB will not check it's structure! |
| `cdbTable->select(XXXX)` | Select rows from the table, see explanation of XXXX below! Returns resouce `cdbRes` |
| -- | -- | 
| `cdbRes->sort(YYYY)` | Sort rows in the resource, see explanation of YYYY below! |
| `cdbRes->sortfn('func')` | Sort rows in the resource via usort with a custom function (PHP 5.3+) |
| `cdbRes->count()` | Count all the rows in this resource |
| `cdbRes->fetch()` | Fetch all the rows in this resource |
| `cdbRes->delete()` | Fetch all the rows in this resource from the table |
| `cdbRes->update(ZZZZ)` | Update all the rows in this resource from the table, see explanation of ZZZZ below |


#### Parameter explanation
**XXXX** is the multi array parameter for select. You can add multiple keys to filter for, for example `['type', '==', 'user']` would search for all rows where the column `type` equals 'user'. Hereby, the first element is the **key** to search for, the second one the **compare operator** (`<`, `<=`, `==`, `>=` or `>`), the third one the respective **value** to compare and the optional fourth parameter can be an `'and'`. You can also chain select parameters as following:  
`...->select(['type', '==', 'user'],['type', '==', 'admin'])->...` Search for rows with the type `user` **OR** `admin`  
`...->select(['type', '==', 'user'],['name', '==', 'cybrox', 'and'])->...` Search for rows with the type `user` **AND** the `name` 'cybrox'  
  
**YYYY** is the multi array parameter for sorting. You can add multiple keys to sort by, for exmaple `['name']` or `['name', SORT_DESC]`. Each array can have up to 3 parameters, that being the column name to sort on (mandatory), either SORT_ASC or SORT_DESC (optional) and a projection function (optional).  
You can also chain sort parameters as following:  
`['type', SORT_DESC], ['name', SORT_DESC]` Sort by number descending and then by name descending.

**ZZZZ** is the multi array parameter for updating. You can add multiple key-vaule pairs to update here like `['name','cybrox']` or `['type','admin'],['name','cybrox']`
You can chain as many fields in there as you want to change.


#### On chaining actions
Though chaining a lot of arguments might get a bit long, it's definitely a really simple way to build *queries* for something like this. You can find a lot of examples on how to chain them in the *example.php* file.


### Some code examples
```
<?php
  $cdb->version()
  $cdb->table('cookies')->exists()
  $cdb->table('cookies')->create()
  $cdb->table('cakes')->create()
  $cdb->table('cakes')->alter('cheese')
  $cdb->tables()
  $cdb->table('cookies')->count()
  $cdb->table('cookies')->insert(array("type" => "chocolate", "is" => "nice"))
  $cdb->table('cookies')->insert(array("type" => "banana", "is" => "nice"))
  $cdb->table('cookies')->insert(array("type" => "strawberry", "is" => "ok"))
  $cdb->table('cookies')->raw()
  $cdb->table('cookies')->select('*')->fetch()
  $cdb->table('cookies')->select('*')->count()
  $cdb->table('cookies')->select(['type', '==', 'chocolate'])->fetch()
  $cdb->table('cookies')->select(['type', '==', 'chocolate'],['type', '==', 'banana', 'or'])->fetch()
  $cdb->table('cookies')->select(['type', '==', 'chocolate'],['type', '==', 'banana', 'and'])->count()
  $cdb->table('cookies')->select('*')->sort(['type'])->fetch()
  $cdb->table('cookies')->select(['type', '==', 'strawberry'])->delete()
  $cdb->table('cookies')->select(['type', '==', 'banana'])->update(['type', 'chocolate'])
  $cdb->table('cookies')->select('*')->fetch()
?>
```