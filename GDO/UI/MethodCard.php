<?php
namespace GDO\UI;

use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Util\Common;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_ResponseCard;

/**
 * Abstract method to render a single GDO as a card.
 * @author gizmore
 * @version 6.11.0
 * @since 6.6.4
 */
abstract class MethodCard extends Method
{
    /**
     * @return GDO
     */
    public abstract function gdoTable();
    
    public function idName() { return 'id'; }

    public function getID() : ?string { return Common::getRequestString($this->idName()); }
    
    public function gdoParameters() : array
    {
        return [
            GDT_Object::make($this->idName())->table($this->gdoTable())->notNull(),
        ];
    }
    
    /**
     * @return GDO
     */
    public function getObject()
    {
        return $this->gdoTable()->find($this->getID());
    }
    
    public function execute() : GDT
    {
        $gdo = $this->getObject();
        if (!$gdo)
        {
            return $this->error('err_no_data_yet');
        }
        return GDT_ResponseCard::newWith()->gdo($gdo);
    }
    
    public function getTitle()
    {
        if ($gdo = $this->getObject())
        {
            return $gdo->displayName();
        }
        return parent::getTitle();
    }
    
}
