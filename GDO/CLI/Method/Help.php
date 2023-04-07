<?php
namespace GDO\CLI\Method;

use GDO\CLI\CLI;
use GDO\CLI\MethodCLI;
use GDO\Core\GDO_Module;
use GDO\Core\GDT;
use GDO\Core\GDT_MethodSelect;
use GDO\Core\GDT_String;
use GDO\Core\Method;
use GDO\Core\ModuleLoader;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\UI\TextStyle;
use GDO\User\GDO_User;

/**
 * Display help for a method.
 *
 * @author gizmore
 */
final class Help extends MethodCLI
{

	public function getCLITrigger(): string { return 'help'; }

	public function createForm(GDT_Form $form): void
	{
		$form->addFields(
			GDT_MethodSelect::make('method')->onlyPermitted(false)->positional(),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addFields(
			GDT_Submit::make(),
		);
	}

	public function formValidated(GDT_Form $form): GDT
	{
		if ($method = $this->getParameterMethod())
		{
			$help = CLI::renderCLIHelp($method);
			return GDT_String::make()->var($help);
		}
		return $this->showAllCommands();
	}

	private function getParameterMethod(): ?Method
	{
		return $this->gdoParameterValue('method');
	}

	private function showAllCommands(): GDT
	{
		$user = GDO_User::current();
		$back = [];
		$grps = [];
		foreach (Method::$CLI_ALIASES as $alias => $command)
		{
			$me = call_user_func([$command, 'make']);
			/** @var Method $me **/
			$name = $me->getModuleName();
			$grps[$name] = isset($grps[$name]) ? $grps[$name] : [];
			$grps[$name][] = $me;
		}
		foreach ($grps as $mo => $mes)
		{
			$mo = ModuleLoader::instance()->getModule($mo);
			$triggers = [];
			/** @var Method $me * */
			foreach ($mes as $me)
			{
				if (($me->isCLI()) &&
					(null === $me->checkPermission($user, true)) &&
					(!$me->isAjax()))
				{
					$triggers[] = $me->getCLITrigger();
				}
			}
			if (count($triggers))
			{
				$back[] = sprintf('%s: %s.', TextStyle::bold($mo->getCLITrigger()), implode(', ', $triggers));
			}
		}

		return GDT_String::make()->var(implode(' ', $back));
	}

}
