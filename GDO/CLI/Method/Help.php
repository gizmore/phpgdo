<?php
declare(strict_types=1);
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
 * Display help for a method or a command overview.
 *
 * @author gizmore
 */
final class Help extends MethodCLI
{

	public function getCLITrigger(): string
	{
		return 'help';
	}

	protected function createForm(GDT_Form $form): void
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
		$grps = [];
		$trgs = [];
		foreach (Method::$CLI_ALIASES as $alias => $command)
		{
			$me = call_user_func([$command, 'make']);
			/** @var Method $me **/
			$name = $me->getModuleName();
			$grps[$name] = isset($grps[$name]) ? $grps[$name] : [];
			$grps[$name][] = $me;
		}
		foreach ($grps as $mon => $mes)
		{
			$mo = ModuleLoader::instance()->getModule($mon);
			$triggers = [];
			/** @var Method $me * */
			foreach ($mes as $me)
			{
				if (($me->isCLI()) &&
					(!$me->isHiddenMethod()) &&
					(null === $me->checkPermission($user, true)) &&
					(!$me->isAjax()))
				{
					$triggers[] = $me;
				}
			}
			if (count($triggers))
			{
				usort($triggers, function (Method $a, Method $b)
				{
					return $a->priority - $b->priority;
				});

				$trgs[$mon] = $triggers;
			}
		}

		$back = [];
		ksort($trgs);
		foreach ($trgs as $mon => $triggers)
		{
			$aliases = array_map(function (Method $me) {
				return $me->getCLITrigger();
			}, $triggers);
			$back[] = sprintf('%s: %s.', TextStyle::bold($mon), implode(', ', $aliases));
		}

		return GDT_String::make()->var(implode(' ', $back));
	}

}
