<?phpnamespace GDO\UI\tpl;
/** @var $field \GDO\UI\GDT_Badge **/?><div class="gdt-badge"><?php if (\GDO\Core\Application::$MODE === \GDO\Core\GDT::RENDER_CELL) : ?><?=$field->htmlIcon()?><?php else: ?><label><?=$field->htmlIcon()?><?=$field->renderLabel()?></label><?php endif; ?><i><?=$field->getVar()?></i></div>
