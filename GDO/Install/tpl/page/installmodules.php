<h2><?=t('install_title_4');?></h2>
<?php

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Template;
use GDO\DB\ArrayResult;
use GDO\Form\GDT_Hidden;
use GDO\Form\GDT_Submit;
use GDO\Install\Config;
use GDO\Table\GDT_Table;
use GDO\UI\GDT_Panel;

echo GDT_Panel::make()->text('install_modules_info_text')->render();

/**
 * @var array $modules
 */
$table = GDT_Table::make()->result(new ArrayResult($modules, GDO_Module::table()));
$table->addHeaderField(GDT_Template::make()->template('Install', 'cell/installcbx.php')->templateHead('Install', 'cell/installcbx_head.php'));
$table->addHeaderField(GDO_Module::table()->gdoColumn('module_name'));
$table->addHeaderField(GDT_Template::make('module_license')->label('license')->template('Install', 'cell/modulelicense.php'));
$table->addHeaderField(GDT_Template::make('module_name')->template('Install', 'modulename_html.php'));
$table->addHeaderField(GDO_Module::table()->gdoColumn('module_priority'));
// $table->addHeader(GDT_ModuleFeature::make('module_features'));
// $table->addHeader(GDT_Text::make('module_description')->method('displayModuleDescription'));
// $table->fetchAs();
$table->fetchInto(false);
$install = GDT_Submit::make('btn_install')->label('install');
$skip = Config::linkStepGDT('5');
$hiddenStep = GDT_Hidden::make('step')->var('4');
$table->actions()->addFields($install, $skip, $hiddenStep);
// $table->noFormWrap();
$table->multisort('module_name');
echo $table->gdo(GDO_Module::table())->render();
?>
<script type="text/javascript">
    var modules = <?=json($moduleNames)?>;
    var coreModules = <?=json($coreModules)?>;
    var siteModules = <?=json($siteModules)?>;
    var dependencies = <?=json($dependencies)?>;
    var siteModule = null;

    function onlyUnique(value, index, self) {
        return self.indexOf(value) === index;
    }

    function enableCoreModules() {
        for (var i in coreModules) {
            enableModule(coreModules[i]);
        }
    }

    function enableModule(module, enabled = true) {
        var cbx = document.getElementById('cbx-module-' + module);
        if (cbx) {
            cbx.checked = enabled;
        }
    }

    function toggledModule(cbx, module) {
        var chk = cbx.checked;
        if (chk) {
            if (isSiteModule(module)) {
                if (siteModule) {
                    alert('<?=t('err_multiple_site_modules')?>');
                }
                siteModule = module;
            }
            var missing = tryToEnableDependencies(module);
            if (missing.length) {
                alert('<?=t('err_missing_dependency')?>' + missing.join(', '));
            }
        } else {
            if (isSiteModule(module)) {
                siteModule = null;
            }
            if (isCoreModule(module)) {
                cbx.checked = true;
                alert('<?=t('err_disable_core_module')?>');
            }
        }
    }

    function isModule(module) {
        return modules.indexOf(module) >= 0;
    }

    function isSiteModule(module) {
        return siteModules.indexOf(module) >= 0;
    }

    function isCoreModule(module) {
        return coreModules.indexOf(module) >= 0;
    }

    function tryToEnableDependencies(module) {
        var deps = [module];
        var lastLength = -1;
        var missing = [];
        while (lastLength != deps.length) { // As long as something changed
            lastLength = deps.length; // Nothing changed as long as we dont add.
            for (var i in deps) {
                var mod = deps[i];
                for (var j in dependencies[mod]) {
                    var dep = dependencies[mod][j];
                    deps.push(dep);
                }
            }
            deps = deps.filter(onlyUnique);
        }

        for (var i in deps) {
            var mod = deps[i];
            if (!isModule(mod)) {
                missing.push(mod);
            }
        }

        enableDependencies(deps);

        return missing;
    }

    function enableDependencies(deps) {
        for (var i in deps) {
            enableModule(deps[i]);
        }
    }

    function enableInstalled() {
        var modules = document.querySelectorAll('.module-installed');
        console.log(modules);
        for (var i in modules) {
            var module = modules[i];
        }

    }

    enableInstalled();
    enableCoreModules();

</script>
