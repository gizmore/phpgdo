<?php
namespace GDO\User\tpl;
use GDO\User\GDT_ProfileLink;
use GDO\User\GDO_User;
/** @var $field GDT_ProfileLink **/
/** @var $user GDO_User **/
$href = $field->hrefProfile();
?>
<span class="gdt-profile-link">
<?php if ($field->avatar) : ?>
  <a href="<?=$href?>" class="gdt-avatar"><?=$field->renderAvatar()?></a>
<?php endif; ?>
<?php if ($field->showNickname()) : ?>
  <a href="<?=$href?>" class="gdt-nickname"><?=$user->renderUserName()?></a>
<?php endif; ?>
</span>
