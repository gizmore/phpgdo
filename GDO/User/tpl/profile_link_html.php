<?php
namespace GDO\User\tpl;

use GDO\User\GDT_ProfileLink;
use GDO\User\GDO_User;

/** @var $field GDT_ProfileLink **/
/** @var $user GDO_User **/
?>
<a href="<?=$field->hrefProfile()?>" class="gdt-profile-link">
<?php if ($field->avatar) : ?>
  <?=GDT_Avatar::for($user)->renderHTML()?>
<?php endif; ?>
<?php if ($field->showNickname()) : ?>
  <?=$user->renderUserName()?>
<?php endif; ?>
</a>
