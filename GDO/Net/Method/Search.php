<?php
namespace GDO\Net\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Net\HTTP;

/**
 * Get duckduckgo search results via the API.
 */
final class Search extends MethodForm
{

    public function getDDGURL(string $query): string
    {
        return sprintf("http://api.duckduckgo.com/?q=%s&format=json", urlencode($query));
    }

    protected function createForm(GDT_Form $form): void
    {
        $form->addFields(
            GDT_String::make('query')->notNull(),
            GDT_AntiCSRF::make(),
        );
        $form->actions()->addFields(GDT_Submit::make());
    }

    public function formValidated(GDT_Form $form): GDT
    {
        $url = $this->getDDGURL($form->getFormVar('query'));
        $res = HTTP::getFromURL($url);
        $data = json_decode($res, true);
        $out = $this->prepareOutput($data);
        return $this->message('msg_ddg_result', [$out]);
    }

    private function prepareOutput(array $data)
    {
        if (@$data['answer'])
        {
            return $data['answer'];
        }

    }

}
