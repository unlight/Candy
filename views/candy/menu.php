<?php if (!defined('APPLICATION')) exit(); 


$Options = array();
$Options[] = Anchor(T('Sections'), 'candy/section/tree', 'Button SmallButton');
//if (C('Debug')) $Options[] = Anchor('Check tree', 'candy/node/check', 'Button SmallButton');
$Options[] = Anchor(T('Pages'), 'candy/page/browse', 'Button SmallButton');
$Options[] = Anchor(T('Add page'), 'candy/page/addnew', 'Button SmallButton');
?>

<div class="FilterMenu" style="margin-bottom: 10px;">
<?php echo implode("\n", $Options); ?>
</div>