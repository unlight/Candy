<?php if(!defined('APPLICATION')) die(); 

$Headline = $this->Data('Headline');

?>

<?php 
if ($Headline) {
	echo '<h1>', $Headline, '</h1>';
	$this->FireEvent('AfterRenderHeadline');
}
?>

<div class="Body"><?php	echo $this->ContentBodyHtml;?></div>
