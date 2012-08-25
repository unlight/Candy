<?php if (!defined('APPLICATION')) die(); ?>

<h1><?php echo $this->Data('Title');?></h1>

<?php 
if ($this->DeliveryType() == DELIVERY_TYPE_ALL) include $this->FetchViewLocation('menu', 'candy'); ?>

<div id="Mask_Form">
<?php echo $this->Form->Open(); ?>
<?php echo $this->Form->Errors(); ?>

<ul class="EditForm">
<?php 
foreach ($this->MaskInfo as $Mask => $Info) {
	echo '<li>';
	echo $this->Form->TextBox("Mask_{$Mask}", array('class' => 'InputBox MaskValue', 'value' => $this->Form->GetValue("Mask_{$Mask}", $Mask)));
	echo $this->Form->TextBox("Description_{$Mask}", array('value' => $this->Form->GetValue("Description_{$Mask}", ArrayValue($Mask, $this->MaskInfo))));
	echo '</li>';
}

echo '<li class="MaskOption">';
echo $this->Form->TextBox('Mask[]', array('class' => 'InputBox MaskValue', 'placeholder' => T('Value')));
echo $this->Form->TextBox('Description[]', array('placeholder' => T('Description')));
echo '</li>';


?>
</ul>
</div>

<?php 
echo $this->Form->Button('Add description', array('class' => 'Button AddDescription'));
if (CheckPermission('Candy.Sections.Edit')) echo $this->Form->Button('Save');
echo $this->Form->Close(); 
?>