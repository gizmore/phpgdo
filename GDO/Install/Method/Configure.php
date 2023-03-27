<?php
namespace GDO\Install\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_String;
use GDO\Core\GDT_Template;
use GDO\DB\Database;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Hidden;
use GDO\Form\GDT_Submit;
use GDO\Form\MethodForm;
use GDO\Install\Config;
use GDO\UI\GDT_Redirect;
use GDO\Util\FileUtil;
use Throwable;

/**
 * Create a GDO config with this form.
 *
 * @version 7.0.2
 * @since 3.0.0
 * @author gizmore
 */
class Configure extends MethodForm
{

	public function isUserRequired(): bool
	{
		return false;
	}

	public function isEnabled(): string
	{
		return true;
	}

	public function isTrivial(): bool
	{
		return false;
	}

	public function gdoParameters(): array
	{
		return [
			GDT_String::make('filename')->initial('config.php'),
			GDT_Hidden::make('step')->initial('3'),
		];
	}

	public function createForm(GDT_Form $form): void
	{
		foreach (Config::fields() as $gdt)
		{
			$form->addField($gdt);
		}
		$form->actions()->addField(GDT_Submit::make('save_config')->onclick([
			$this,
			'onSaveConfig',
		]));
		$enabled = FileUtil::isFile($this->configPath());
		$form->actions()->addField(
			GDT_Submit::make('test_config')->enabled($enabled)
				->onclick([
					$this,
					'onTestConfig',
				]));
	}

	public function configPath(): string
	{
		return GDO_PATH . 'protected/' . $this->cfgConfigName();
	}

	public function cfgConfigName(): string
	{
		return $this->gdoParameterVar('filename');
	}

	# ############
	# ## Write ###
	# ############

	public function onSaveConfig(): GDT
	{
		$this->writeConfig($this->configPath());
		return GDT_Redirect::make()->redirectTime(2)
			->back()
			->redirectMessage('msg_config_written', [
				html($this->cfgConfigName()),
			], Config::hrefStep(3));
	}

	public function writeConfig(string $path): bool
	{
		$form = $this->getForm();
		$content = GDT_Template::php('Install', 'config.php', [
			'form' => $form,
		]);
		FileUtil::createDir(dirname($path));
		return !!file_put_contents($path, $content);
	}

	# ###########
	# ## Test ###
	# ###########
	public function onTestConfig(): GDT
	{
		if (GDO_DB_ENABLED)
		{
			try
			{
				$db = new Database(GDO_DB_HOST, GDO_DB_USER, GDO_DB_PASS, GDO_DB_NAME, false);
				$db->getLink();
			}
			catch (Throwable $ex)
			{
				return $this->error('err_db_connect', [$ex->getMessage()])->addField($this->renderPage());
			}
		}

		$link = Config::linkStep(4);
		return $this->message('install_config_boxinfo_success', [
			$link,
		]);
	}

}
