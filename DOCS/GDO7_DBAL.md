# GDOv7 DBAL

I am very proud of the GDOv7 Database Abstraction Layer. It actually is easier than writing SQL by hand, 
and i don't have to look up trivial tasks like getting an entitiy back from it, or creating complex join conditions.
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
 

## GDOv7 DBAL: Migrations

I bet you don't like writing migrations much.
Good news! In GDOv7 you don't write migrations, you plug together GDT.
To create a database table, simply inherit the GDO class, and overwrite gdoColumns().
Return an array of GDT in all flavours and combinations. That's it.
Changed your DB layout?
GDOv7 got you covered by changing the DB layout on the fly with a single click.
Admitted, this is quite risky and may be not the best technique to manage DB migrations, but for me it works great!
Background info: In an auto-migration the table is exported and re-imported to avoid hackery with SQL.
Works charmy!


## GDOv7 DBAL: Foreign Keys

Creating foreign keys is not the easiest task.
I always have to read up again with any other DBAL.
In GDOv7 you have the [GDT_Object](../GDO/Core/GDT_Object.php) which takes a GDO table as attribute.
It simply knows how to join your relations.
The default cascades are deleting, but with cascadeRestrict() your biggest worries are over.
Of course the many foreign keys are performance hungry.


## GDOv7 DBAL: GDO examples

1) Select all admins


## GDOv7 DBAL: GDO vs. Eloquent


