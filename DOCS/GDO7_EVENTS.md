# GDOv7 Events

## GDOv7 Events: GDO Events

GDT know the following GDO events.
Note that a GDO is also a GDT:

- gdoBeforeCreate(GDO $gdo, Query $query) : void
- gdoBeforeRead(GDO $gdo, Query $query) : void
- gdoBeforeUpdate(GDO $gdo, Query $query) : void
- gdoBeforeDelete(GDO $gdo, Query $query) : void


- gdoAfterCreate(GDO $gdo) : void
- gdoAfterRead(GDO $gdo) : void
- gdoAfterUpdate(GDO $gdo) : void
- gdoAfterDelete(GDO $gdo) : void

## GDOv7 Events: Module Hooks

Module hooks are GDO_Module methods that start with "*hook*", literally.

    public function hookUserActivated(GDO_User $user)
    {
        # do something
    }

## GDOv7 Events: List of Hooks

- UserActivated(GDO_User $user, GDO_UserActivation $ua)

- UserDeleted(GDO_User $user)

- UserAuthenticated(GDO_User $user)

- CreateCard{$Module}{$Method}(GDT_Card $card)

## GDOv7 Events: Module Hook IPC

Events can be sent to other GDO servers, f.e. the Websocket module.

## GDOv7 Events: Event engine

@TODO This needs to be designed and developed or stolen.
