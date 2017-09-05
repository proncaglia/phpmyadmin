<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Common header for user preferences pages
 *
 * @package PhpMyAdmin
 */
use PhpMyAdmin\Config\Descriptions;
use PhpMyAdmin\Message;
use PhpMyAdmin\Relation;
use PhpMyAdmin\Sanitize;

if (!defined('PHPMYADMIN')) {
    exit;
}
// build user preferences menu

$form_param = isset($_GET['form']) ? $_GET['form'] : null;
if (! isset($forms[$form_param])) {
    $forms_keys = array_keys($forms);
    $form_param = array_shift($forms_keys);
}
$tabs_icons = array(
    'Features'    => 'b_tblops.png',
    'Sql_queries' => 'b_sql.png',
    'Navi_panel'  => 'b_select.png',
    'Main_panel'  => 'b_props.png',
    'Import'      => 'b_import.png',
    'Export'      => 'b_export.png');

$content = PhpMyAdmin\Util::getHtmlTab(
    array(
        'link' => 'prefs_manage.php',
        'text' => __('Manage your settings')
    )
) . "\n";
$script_name = basename($GLOBALS['PMA_PHP_SELF']);
foreach (array_keys($forms) as $formset) {
    $tab = array(
        'link' => 'prefs_forms.php',
        'text' => Descriptions::get('Form_' . $formset),
        'icon' => $tabs_icons[$formset],
        'active' => ($script_name == 'prefs_forms.php' && $formset == $form_param));
    $content .= PhpMyAdmin\Util::getHtmlTab($tab, array('form' => $formset))
        . "\n";
}
echo PhpMyAdmin\Template::get('list/unordered')->render(
    array(
        'id' => 'topmenu2',
        'class' => 'user_prefs_tabs',
        'content' => $content,
    )
);
echo '<div class="clearfloat"></div>';

// show "configuration saved" message and reload navigation panel if needed
if (!empty($_GET['saved'])) {
    Message::rawSuccess(__('Configuration has been saved.'))->display();
}

// warn about using session storage for settings
$cfgRelation = Relation::getRelationsParam();
if (! $cfgRelation['userconfigwork']) {
    $msg = __(
        'Your preferences will be saved for current session only. Storing them '
        . 'permanently requires %sphpMyAdmin configuration storage%s.'
    );
    $msg = Sanitize::sanitize(
        sprintf($msg, '[doc@linked-tables]', '[/doc]')
    );
    Message::notice($msg)->display();
}
