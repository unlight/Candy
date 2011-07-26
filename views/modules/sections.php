<?php if (!defined('APPLICATION')) exit(); 


$ViewingSectionID = isset($this->_Sender->SectionID) ? $this->_Sender->SectionID : '';

// Need check with applications/vanilla/views/modules/categories.php
?>


<div class="Box BoxSections">
	<?php /*<h4><?php echo Anchor(T('Sections'), 'content/map'); ?></h4>*/ ?>
	<ul class="PanelInfo">

<?php
$MaxDepth = -1; // TODO: MaxDepth SET TO CONFIG
foreach ($this->Data() as $Section) {
	if ($MaxDepth > 0 && $Section->Depth > $MaxDepth) continue;

	$CssClass = 'Depth'.$Section->Depth.($ViewingSectionID == $Section->SectionID ? ' Active' : '');
	
	echo '<li class="'.$CssClass.'">';
	//echo Wrap(Anchor(($Section->Depth > 1 ? '↳ ' : '').Gdn_Format::Text($Section->Name), '/categories/'.rawurlencode($Category->UrlCode)), 'strong');
    echo ($Section->Depth > 1 ? '↳ ' : '');
    echo SectionAnchor($Section);
    //echo '<span class="Count">?</span>';
	//echo "&#160;</li>\n";
	echo "</li>\n";
}

?>

</ul>
</div>
