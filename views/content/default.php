<?php if(!defined('APPLICATION')) die(); 

$Content = $this->Data('Content');

$this->EventArguments['Format'] =& $Content->Format;
$this->EventArguments['Body'] =& $Content->Body;
$this->FireEvent('BeforeBodyFormat');
$BodyFormat = Gdn_Format::To($Content->Body, $Content->Format);

$TextHeader = '';
$Doc = PqDocument($BodyFormat);
$Header = $Doc->Find('h1');
if (count($Header) == 0) $TextHeader = Gdn_Format::Text($Content->Title);
elseif (count($Header) == 1) {
	$TextHeader = $Header->Text();
	$Header->Remove();
	$BodyFormat = $Doc->Html();
}

?>

<?php 
if ($TextHeader) {
	echo '<h1>', $TextHeader, '</h1>';
	$this->FireEvent('AfterRenderHeadline');
}
?>

<div class="Body"><?php	echo $BodyFormat;?></div>
