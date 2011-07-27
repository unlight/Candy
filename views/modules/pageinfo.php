<?php if (!defined('APPLICATION')) exit(); 

$Page = GetValue('Content', $Data);
$Title = GetValue('Title', $Data, $Page->Title);
$TargetUrl = $this->_Sender->SelfUrl;
// Wrapper box <div class="Box"> in Module::String()
?>


<h4><?php echo Anchor($Title, 'content/page/'.$Page->PageID.'/'.CleanupString($Page->Title));?></h4>

<ul class="PanelInfo">
	<li><strong><?php echo Anchor(T('Edit'), 'candy/page/edit/'.$Page->PageID, ''); ?></strong>&#160;</li>
	<?php /*<li><?php echo Anchor(T('Delete'), 'candy/page/delete/'.$Page->PageID, ''); ?></li> */?>
	<li><strong><?php echo Anchor(T('Published'), 'candy/page/visible/'.$Page->PageID.'?Target='.$TargetUrl, 'BoolButton'); ?><?php
		echo T($Page->Visible == 1? 'Yes' : 'No');
	?></strong></li>
</ul>
