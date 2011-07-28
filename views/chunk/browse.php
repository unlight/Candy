<?php if (!defined('APPLICATION')) exit();

?>

<h1><?php echo $this->Data('Title');?></h1>


<?php include $this->FetchViewLocation('menu', 'candy'); ?>


<?php if ($this->Chunks->NumRows() == 0) {
	echo "<div class='Info Empty'>".T('There are nothing.')."</div>";
	return;
}
?>

<table class="AltRows" style="width:100%">

<tbody>
<?php foreach ($this->Chunks as $Chunk) {
	//d($Chunk);
	$Id = $Chunk->ChunkID;
	$Name = $Chunk->Name;
	$Options = array();
	$Options[] = Anchor(T('Edit'), 'candy/chunk/update/'.$Id, '');
	$Options[] = Anchor(T('Delete'), 'candy/chunk/delete/'.$Id, 'DeleteItem');
	?>
	<tr>
	<td><?php echo $Id;?></td>
	<td><?php echo $Name;?></td>
	<td><?php echo Gdn_Format::Date($Chunk->DateUpdated);?></td>
	<td class="Options"><?php echo implode('', $Options);?></td>
	</tr>
	<?php } ?>
</tbody>
</table>



