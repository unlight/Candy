<?php if (!defined('APPLICATION')) die(); 

$Content = $this->Data('Content');
$Session = Gdn::Session();
?>

<h1><?php echo $this->Data('Title'); 
if ($this->Editing) echo ', Request URI: ' . Anchor('candy/content/page/'.$Content->PageID, 'candy/content/page/'.$Content->PageID);
?></h1>

<?php include $this->FetchViewLocation('menu', 'candy'); ?>

<?php 
echo $this->Form->Open(array('enctype' => 'multipart/form-data'));
echo $this->Form->Errors(); 
?>


<ul class="EditForm">
<?php 
$this->FireEvent('BeforeInputFieldsRender'); 
echo Wrap($this->Form->TextBox('Title', array('placeholder' => T('Title'))), 'li');
echo Wrap(
	$this->Form->Label('Published', 'Visible').
	$this->Form->CheckBox('Visible'),
	'li'
);
$FormatOptions = LocalizedOptions(array('Text', 'xHtml', 'Html', 'Markdown', 'Raw'));
if (!CheckPermission('Candy.Pages.Raw')) unset($FormatOptions['Raw']);
$this->EventArguments['FormatOptions'] =& $FormatOptions;
$this->FireEvent('FormatOptions');
echo Wrap(
	$this->Form->Label('Format', 'Format').
	$this->Form->DropDown('Format', $FormatOptions),
	'li'
);
$DropDownOptions = array('IncludeNull' => True, 'TextField' => 'Name', 'ValueField' => 'SectionID');
echo Wrap(
	$this->Form->Label('Section', 'SectionID').
	$this->Form->DropDown('SectionID', $this->Tree, $DropDownOptions),
'li');

if (!$this->Editing) {
	echo Wrap(
		$this->Form->Label('Create section', 'CreateSection').
		$this->Form->CheckBox('CreateSection'), 
		'li'
	);
}

echo Wrap(
	$this->Form->Label('URI', 'URI').
	$this->Form->TextBox('URI'), 
	'li'
);

if ($Session->CheckPermission('Candy.Pages.Meta')) {
	echo Wrap(
		$this->Form->Label('MetaTitle', 'MetaTitle') .
		$this->Form->TextBox('MetaTitle'), 
		'li', array('class' => 'MetaFields Hidden'));
	echo Wrap(
		$this->Form->Label('Meta tag (description)', 'MetaDescription') .
		$this->Form->TextBox('MetaDescription', array('Multiline' => True)),
		'li', array('class' => 'MetaFields Hidden'));
	echo Wrap(
		$this->Form->Label('Meta tag (keywords)', 'MetaKeywords') .
		$this->Form->TextBox('MetaKeywords', array('Multiline' => False)), 
		'li', array('class' => 'MetaFields Hidden'));
	echo Wrap(
		$this->Form->Label('Meta tag (robots)', 'MetaRobots') .
		$this->Form->TextBox('MetaRobots', array('Multiline' => False)), 
		'li', array('class' => 'MetaFields Hidden'));
}

$Forms = $Buttons = array();

$Buttons[] = Wrap(T('Body text?'), 'a', array('href' => 'javascript:', 'class' => 'TabToggleButton BodyTextBox'));
if ($Session->CheckPermission('Candy.Pages.Meta')) $Buttons[] = Wrap(T('Meta tags?'), 'a', array('href' => 'javascript:', 'class' => 'ToggleButton MetaFields'));
$Buttons[] = Wrap(T('Slug from title?'), 'a', array('href' => 'javascript:', 'class' => '', 'id' => 'GetSlugButton'));

$Forms[] = $this->Form->TextBox('Body', array('Multiline' => True, 'placeholder' => T('Body'), 'class' => 'TextBox BodyTextBox CodeBox'));
if ($Session->CheckPermission('Candy.Pages.Raw')) {
	$Forms[] = $this->Form->TextBox('CustomCss', array('Multiline' => True, 'placeholder' => T('Custom CSS rules'), 'class' => 'TextBox CustomCss CodeBox Hidden'));
	$Forms[] = $this->Form->TextBox('CustomJs', array('Multiline' => True, 'placeholder' => T('Custom JavaScript'), 'class' => 'TextBox CustomJs CodeBox Hidden'));
	$Buttons[] = Wrap(T('Custom css?'), 'a', array('href' => 'javascript:', 'class' => 'TabToggleButton CustomCss'));
	$Buttons[] = Wrap(T('Custom js?'), 'a', array('href' => 'javascript:', 'class' => 'TabToggleButton CustomJs'));
}

echo Wrap(
	'<span class="Buttons">' . implode('', $Buttons) . '</span>' . 
	implode("\n", $Forms),
	'li', array('class' => '')
);

$this->FireEvent('AfterInputFieldsRender'); 
?>

</ul>
<?php 
echo $this->Form->Button('Save');
echo ($this->Editing) ? $this->Form->Button('Delete') : '';
$this->FireEvent('BeforeCloseForm');
echo $this->Form->Close();
?>
