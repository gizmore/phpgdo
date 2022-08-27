# GDOv7 DBAL

I am very proud of the GDOv7 Database Abstraction Layer. It actually is easier than writing SQL by hand, 
and i don't have to look up trivial tasks on google,
like getting an entitiy back from it,
or creating join conditions.
The API is very consistent and enjoyable to use.
It might not have a fancy and quirky *hasMany*,
this can be realized by adding a GDT_Join to your GDO,
instead it allows to have composite primary keys.

The GDO DBAL consists of the [Module_DB](../GDO/DB) files as well as the [GDO](../GDO/Core/GDO.php) class.
In summary:

 - [GDO.php](../GDO/Core/GDO.php) (Main DBAL Logic)
 - [Cache.php](../GDO/DB/Cache.php) (All caches)
 - [Database.php](../GDO/DB/Database.php) (Connection interface)
 - [Query.php](../GDO/DB/Query.php) (Query Builder)
 - [Result.php](../GDO/DB/Result.php) (Result Set)
 - [ArrayResult.php](../GDO/DB/ArrayResult.php) (Result Set, manually filled)


## GDOv7 DBAL: Drawbacks

Nothing comes without a tradeoff.
The GDOv7 code is very basic, yet suprisingly stable and clever.
The performance is also possible because i simply don't care that you may not have reserved words as identifiers.
You cannot have this in GDOv7, but in exchange you never need to quote or escape a single table or column name. Deal!
 
 
## GDOv7 DBAL: IDs

In some DBAL it is convention to have an auto inc as the primary key.
In GDOv7, there is no convention or restriction on how to key your tables.
We sometimes have a CHAR(2) for maybe country, or maybe a composite primary key. All is possible and intuitive.
Maybe look at the implementation of [GDT_AutoInc](../GDO/Core/GDT_AutoInc.php) and [GDT_Char](../GDO/Core/GDT_Char.php).


## GDOv7 DBAL: Migrations

I bet you don't like writing migrations much.
Good news! In GDOv7 you don't write migrations, you plug together GDT.
To create a database table, simply inherit the GDO class, and overwrite gdoColumns().
Return an array of GDT in all flavours and combinations. That's it.
Changed your DB layout?
GDOv7 got you covered by changing the DB layout on the fly with a single click.
Admitted, this is quite risky and may be not the best technique to manage DB migrations, but for me it works great!
Background info: In an auto-migration the table is **exported**, **dropped** and then **re-imported** to avoid hackery with SQL.
Works charmy!
It probably is slow and too risky in real production environments.
We will see :)


## GDOv7 DBAL: Foreign Keys

Creating foreign keys is not the easiest task.
I always have to read up again with any other DBAL.
In GDOv7 you have the [GDT_Object](../GDO/Core/GDT_Object.php) which takes a GDO table as attribute.
It simply knows how to join your relations.
The default cascades are deleting, but with cascadeRestrict() your biggest worries are over.
Of course the many foreign keys are performance hungry.


## GDOv7 DBAL: GDO examples

1) Get a user by name

    GDO_User::table()->select() # Select * from gdo_user
    ->where('user_name="gizmore"') # Add Where condition
    ->first() # Short for ->limit(0, 1) # Still building Query object
    ->exec() # Get Result object from Query object
    ->fetchObject() # Result fetches GDO_User object
    

2) Select all admins

    GDO_UserPermission::table()->select('perm_user_id_t.*') # Select only  GDO_User columns. Query the permission relation table.
    ->joinObject('perm_user_id') # Join the user table. perm_user_id is the column that references the user table. it is automatically joined as perm_user_id_t
    ->joinObject('perm_perm_id') # Join the permission table, so we can query permissions by name, not by id.
    ->where('perm_name="admin"') # Select all admins :)
    ->exec() # execute the query and get a Result
    ->fetchTable(GDO_User::table()) # Set result fetch class to GDO_User. Else it would fetch GDO_UserPermission objects.
    ->fetchAllObjects() # get an array of GDO_User objects

3) A union select

    $query1 = GDO_User::table()->where('user_type="member"'); # simple query 1
    $query2 = GDO_User::table()->where('user_type="guest"'); # simple query 2
    $query1->union($query2)->exec()->fetchAll(); # Use union to merge the two queries.


## GDOv7 DBAL: GDO vs. Eloquent

Eloquent does not allow composite primary keys.


##
