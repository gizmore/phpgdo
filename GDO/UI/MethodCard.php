<?php
namespace GDO\UI;

use GDO\Core\Method;
use GDO\Core\GDO;
use GDO\Core\GDT_Object;
use GDO\Core\GDT;
use GDO\Core\GDT_Hook;

/**
 * Abstract method to render a single GDO as a card.
 * 
 * @author gizmore
 * @version 7.0.0
 * @since 6.6.4
 */
abstract class MethodCard extends Method
{
    public function idName() : string { return 'id'; }

    public abstract function gdoTable() : GDO;

    ##############
    ### Params ###
    ##############
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
    
    ############
    ### Exec ###
    ############
    public function execute()
    {
        $gdo = $this->getObject();
        if (!$gdo)
        {
            return $this->error('err_no_data_yet');
        }
        return $this->executeFor($gdo);
    }
    
    protected function executeFor(GDO $gdo) : GDT
    {
    	$card = GDT_Card::make()->gdo($gdo);
    	$this->createCard($card);
    	GDT_Hook::callHook("CreateCard{$this->getModuleName()}{$this->getMethodName()}", $card);
    	return $card;
    }
    
    protected function createCard(GDT_Card $card) : void  
    {
    }
    
    ###########
    ### Seo ###
    ###########
    public function getTitle()
    {
        if ($gdo = $this->getObject())
        {
            return $gdo->renderName();
        }
        return parent::getTitle();
    }
    
}
