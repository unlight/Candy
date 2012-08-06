<?php if (!defined('APPLICATION')) exit(); 

$Options = array();
$SettingsView = CheckPermission('Candy.Settings.View');
if ($SettingsView) {
	$Options[] = Anchor(T('Sections'), 'candy/section/tree', 'SmallButton');
	//if (C('Debug')) $Options[] = Anchor('Check tree', 'candy/node/check', 'Button SmallButton');
	$Options[] = Anchor(T('Pages'), 'candy/page/browse', 'SmallButton');
}
if (CheckPermission('Candy.Pages.Add')) $Options[] = Anchor(T('Add page'), 'candy/page/addnew', 'SmallButton');
if (CheckPermission('Candy.Settings.View')) $Options[] = Anchor(T('Chunks'), 'candy/chunk/browse', 'SmallButton');
if (CheckPermission('Candy.Chunks.Edit')) $Options[] = Anchor(T('Add chunk'), 'candy/chunk/update', 'SmallButton');

if ($SettingsView) $Options[] = Anchor(T('Routes'), 'candy/routes', 'SmallButton');

?>

<div class="FilterMenu" style="padding-bottom: 10px;">
<?php echo implode("\n", $Options); ?>
</div>