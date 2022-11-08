<?php
namespace GDO\User\tpl;
use GDO\User\GDT_ProfileLink;
use GDO\User\GDO_User;
use GDO\Core\GDT_Field;
use GDO\Avatar\GDT_Avatar;
/** @var $field GDT_ProfileLink **/
/** @var $user GDO_User **/
if (!$user)
{
	printf("<span class=\"gdt-profile-link\">%s</span>", t('unknown'));
	return;
}
$href = $field->hrefProfile();
// if ($field->level)
{
	$htmlTitle = t('tt_user_level', [$user->renderUserName(), $user->getLevel()]);
}
$htmlTitle = isset($htmlTitle) ? " title=\"{$htmlTitle}\"" : '';
// $field->avatarSize(18);
?>
<span class="gdt-profile-link"<?=$htmlTitle?>>
<?php if ($field->avatar) : ?>
<a href="<?=$href?>" class="gdt-avatar"><?=GDT_Avatar::make()->user($user)->render()?></a>
<?php endif; ?>
<?php if ($field->nickname) : ?>
<a href="<?=$href?>" class="gdt-nickname"><?=$field->getGDO()->renderUserName()?></a>
<?php endif; ?>
<?php if ((!$field->nickname)&&(!$field->avatar)) : ?>
<i><?=$user->gdoHumanName()?></i>
<?php endif; ?>
</span>
