# GDOv7 DBA*L*

This page is a hail to the
[GDO](../GDO/Core/GDO.php)
[Database]()
[Abstraction]()
[Layer]()

I am very proud of it. Really.

Using GDO is way faster and easier than writing SQL by hand,
and i don't have to look up easy tasks on google for it.
Like getting an entity or a single value back from a raw query.
In every other DBA i saw, creating join conditions,
or wanting to use a native sql function, was plain horrible.
The GDO API is very consistent and a joy to use. (for me)
GDO might not have fancy'n'quirky *hasMany* or *manyToMany* helpers, decorators and it does not use reflection to do havok,
but such a many2many can be realized by, for example,
adding a [GDT_Join](../GDO/Core/GDT_Join.php) column to your GDO,
and simply do `joinObject()` it, when you need it in the query scope.
Another thing is, often you have to write a special query anyway.
Decorating models with reflection is totally non-sense!

# GDOv7 DBAL: Engine Codebase

The GDO DBA consists of the [Module_DB](../GDO/DB) files as well as the [GDO](../GDO/Core/GDO.php) class.
In summary:

- [GDO.php](../GDO/Core/GDO.php) (Main DBAL Logic)
- [GDT.php](../GDO/Core/GDO.php) (Main DBAL Logic)
- [Cache.php](../GDO/DB/Cache.php) (All caches)
- [Database.php](../GDO/DB/Database.php) (Connection interface)
- [Query.php](../GDO/DB/Query.php) (Query Builder)
- [Result.php](../GDO/DB/Result.php) (Result Set)
- [ArrayResult.php](../GDO/DB/ArrayResult.php) (Result Set, manually filled)

# GDOv7 DBAL: Column GDTs

In GDOv7, almost everything is a Gizmore Data Type, even GDOs.

You can plug any Type inside your GDO table,
and get a lot of repetetive work done by your own datatypes.

Validation has never been *dry* easier.

## GDOv7 DBAL: IDs

In quite some DBAL, it is convention, you *must* have an auto inc as the primary key column.
Here is no convention or restriction on how to key your tables.
We sometimes have a CHAR(2) for maybe country, or maybe a composite primary key. All is possible and intuitive.
Maybe look at the implementation of
[GDT_AutoInc](../GDO/Core/GDT_AutoInc.php) and
[GDT_Char](../GDO/Core/GDT_Char.php).

## GDOv7 DBAL: Migrations

I bet you don't like writing migrations much.
Good news! In GDOv7 you don't write migrations, you plug together GDT.
To create a database table, simply inherit the GDO class, and overwrite gdoColumns().
Return an array of GDT in all flavours and combinations. That's it.
Changed your DB layout?
GDOv7 got you covered by changing the DB layout on the fly with a single click.
Admitted, this is quite risky and may be not the best technique to manage DB migrations, but for me it works great!
Background info: In an auto-migration the table is **exported**, **dropped** and then **re-imported** to avoida any hackery with SQL. The auto migration code
for mysql is around 100 lines.
Works charmy!
But it probably is slow and too risky in real production environments.

We will see :)

## GDOv7 DBAL: Foreign Keys

Creating foreign keys is not the easiest task.
I always have to read up again with any other DBAL.
In GDOv7 you have the [GDT_Object](../GDO/Core/GDT_Object.php) which takes a GDO table as attribute.
It simply knows how to join your relations.
The default cascades are deleting, but with cascadeRestrict() your biggest worries are over.
Of course the many foreign keys are performance hungry.

## GDOv7 DBAL: Transactions

In GDOv7, when using InnoDB as the `GDO_DB_ENGINE`,
transactions are created appropriately for the request.
But only when you are doing a *POST*, **and** the method wants it.
[MethodForm](../GDO/Form/MethodForm.php)
should be the only generic Method that behaves like this.

## GDOv7 DBAL: GDO examples

I would not be unhappy, if someone would issue a few problems,
so let's get to work with some examples.

1) Get a user by name

   GDO_User::table()->select() # Select * from gdo_user
   ->where('user_name="gizmore"') # Add Where condition
   ->first() # Short for ->limit(0, 1) # Still building Query object
   ->exec() # Get Result object from Query object
   ->fetchObject() # Result fetches GDO_User object


2) Select all users of a group by group name

   GDO_UserPermission::table()->select('perm_user_id_t.*') # Select only GDO_User columns. Query the permission relation table.
   ->joinObject('perm_user_id') # Join the user table. perm_user_id is the column that references the user table. it is automatically joined as perm_user_id_t
   ->joinObject('perm_perm_id') # Join the permission table, so we can query permissions by name, not by id.
   ->where('perm_name="admin"') # Select all admins :)
   ->exec() # execute the query and get a Result
   ->fetchTable(GDO_User::table()) # Set result fetch class to GDO_User. Else it would fetch GDO_UserPermission objects.
   ->fetchAllObjects() # get an array of GDO_User objects


3) A union select

   $query1 = GDO_User::table()->where('user_type="member"'); #simple query 1
   $query2 = GDO_User::table()->where('user_type="guest"'); # simple query 2
   $query1->union($query2)->exec()->fetchAll(); # Use union to merge the two queries.

## GDOv7 DBAL: GDO vs. Eloquent

- also does allow composite primary keys.
- Eloquent seems harder to learn

## GDOv7 DBAL: Drawbacks

Nothing comes without tradeoffs.

The GDOv7 code is very basic, yet suprisingly stable and short.
The performance is also possible because... as an example...
i simply *avoid*, better, i **can't** use reserved words as identifiers.
I don't care! This is by a clever
[design convention]().

Columns have to use a table specific prefix.
Only by convention.

You cannot have "*ID*" as an identifier in GDOv7,
but in exchange, you never need to escape a single identifier,
have to wrap backticks around them,
or have to worry too much about duplicate column names.

GDOv7 has
quite
a
few
similiar
examples.

Read on!

- (c)2023 gizmore 
