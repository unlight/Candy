<?php if(!defined('APPLICATION')) exit(); ?>

<h1><?php echo $this->Data('Title');?></h1>

<div id="ContentMap" class="Body">
<?php
echo "\n<ol class='Tree'>";
$CurrentDepth = 0;
$Counter = 0;
foreach ($this->Tree as $Node) {
	
	if ($Node->Depth > $CurrentDepth) echo "<ul>";
	elseif ($Node->Depth < $CurrentDepth) {
		echo str_repeat("</li></ul>", $CurrentDepth - $Node->Depth), '</li>';
	} else {
		if ($Counter > 0) echo "</li>";
	}
	
	$CurrentDepth = $Node->Depth;
	++$Counter;
	
	$ItemAttribute = array('id' => 'Tree_'.$Node->SectionID);
	
	echo "\n<li".Attribute($ItemAttribute).'>';
	echo SectionAnchor($Node);
}
echo str_repeat("</li></ul>", $Node->Depth) . '</li>';	
echo "</ol>";
?>
</div>