# GDOv7 Events


## GDOv7 Events: GDO Events

GDT know the following GDO events:

- gdoBeforeCreate(Query)
- gdoBeforeRead(Query)
- gdoBeforeUpdate(Query)
- gdoBeforeDelete(Query)

- gdoAfterCreate(Query, GDO)
- gdoAfterRead(Query, GDO)
- gdoAfterUpdate(Query, GDO)
- gdoAfterDelete(Query, GDO)


## GDOv7 Events: Module Hooks

Module hooks are methods that start with "*hook*" literally.


    public function hookUserActivated(GDO_User $user)
    {
        # do something
    }
    
    

## GDOv7 Events: Module Hook IPC

