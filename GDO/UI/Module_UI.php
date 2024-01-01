<?php
namespace GDO\UI;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\Core\GDT_String;
use GDO\Core\GDT_UInt;
use GDO\User\GDO_User;

/**
 * The UI module offers many html rendering widgets and traits.
 * Allows users to choose their message editor.
 *
 * Not limited to:
 *
 * - Icon rendering
 * - Message and Editor rendering
 * - HTML rendering widgets like pre,p,hr,h1,etc
 * - WithPHPJQuery, a jQuery like PHP api. (very rudimentary)
 * - Buttons and Links
 * - Color utility
 * - CLI vs HTML utility
 * - Sliders
 *
 * @version 7.0.2
 * @since 6.1.0
 * @author gizmore
 */
final class Module_UI extends GDO_Module
{

	public int $priority = 20;

	public function isCoreModule(): bool
	{
		return true;
	}

	# #############
	# ## Config ###
	# #############
	public function getConfig(): array
	{
		return [
            GDT_Checkbox::make('allow_editor_choice')->notNull()->initial('1'),
            GDT_Checkbox::make('store_device_info')->notNull()->initial('0'),
			GDT_MessageEditor::make('default_editor')->notNull()->initial('Text'),
		];
	}

	public function getUserSettings(): array
	{
		$settings = [];
		if ($this->cfgAllowEditorChoice())
		{
			$settings[] = GDT_MessageEditor::make('text_editor')->initial($this->cfgDefaultEditor());
		}
		return $settings;
	}

	public function cfgAllowEditorChoice(): bool
	{
		return $this->getConfigValue('allow_editor_choice');
	}

    public function cfgStoreDeviceInfo(): bool
    {
        return $this->getConfigValue('store_device_info');
    }

	################
	### Settings ###
	################

    public function getUserConfig(): array
    {
        return [
            GDT_UInt::make('device_width'),
            GDT_UInt::make('device_height'),
            GDT_String::make('device_version'),
        ];
    }

    public function getPrivacyRelatedFields(): array
    {
        return [
            GDT_Divider::make('privacy_info_ui_module'),
            $this->setting('device_width'),
            $this->setting('device_height'),
            $this->setting('device_version'),
        ];
    }


    public function cfgDefaultEditor(): string
	{
		$editor = $this->getConfigVar('default_editor');
		return $editor ? $editor : GDT_Message::$EDITOR_NAME;
	}

	##############
	### Events ###
	##############

	/**
	 * Upon method execution, set the current users edit decoder.
	 */
	public function hookBeforeExecute(): void
	{
		$user = GDO_User::current();
		$decoder = $user->settingVar('UI', 'text_editor');
		GDT_Message::setDecoder($decoder);
	}

}
