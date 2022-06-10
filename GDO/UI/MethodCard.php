<?php
namespace GDO\UI;

use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Core\GDT_Object;

/**
 * Abstract method to render a single GDO as a card.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.6.4
 */
abstract class MethodCard extends Method
{
    public function idName() { return 'id'; }

    public abstract function gdoTable() : GDO;

    public function gdoParameters() : array
    {
        return [
            GDT_Object::make($this->idName())->table($this->gdoTable())->notNull(),
        ];
    }

    public function getObject() : GDO
    {
    	return $this->gdoParameterValue($this->idName());
    }
    
    public function execute()
    {
        $gdo = $this->getObject();
        if (!$gdo)
        {
            return $this->error('err_no_data_yet');
        }
        return $gdo;
    }
    
    public function getTitle()
    {
        if ($gdo = $this->getObject())
        {
            return $gdo->renderName();
        }
        return parent::getTitle();
    }
    
}
