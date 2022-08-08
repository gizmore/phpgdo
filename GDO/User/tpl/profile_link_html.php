<?php
namespace GDO\User\tpl;
use GDO\User\GDT_ProfileLink;
use GDO\User\GDO_User;
/** @var $field GDT_ProfileLink **/
/** @var $user GDO_User **/
$href = $field->hrefProfile();
if ($field->level)
{
	$htmlTitle = t('tt_user_level', [$user->renderUserName(), $user->getLevel()]);
}
$htmlTitle = isset($htmlTitle) ? " title=\"{$htmlTitle}\"" : '';
?>
<span class="gdt-profile-link"<?=$htmlTitle?>>
<?php if ($field->hasAvatar()) : ?>
  <a href="<?=$href?>" class="gdt-avatar"><?=$field->renderAvatar()?></a>
<?php endif; ?>
<?php if ($field->nickname) : ?>
  <a href="<?=$href?>" class="gdt-nickname"><?=$user->renderUserName()?></a>
<?php endif; ?>
</span>
