<?php if (!defined('APPLICATION')) exit();

Gdn::FactoryInstall('SectionModel', 'SectionModel', 
	PATH_APPLICATIONS.'/candy/models/class.sectionmodel.php', Gdn::FactorySingleton);


if (!function_exists('BuildNode')) {
	/**
	* BuildNode($Object, 'Section')
	* 
	*/
	function BuildNode($Object, $Prefix) {
		$Node = new StdClass();
		$Node->TreeLeft = $Object->{$Prefix.'TreeRight'};
		$Node->TreeRight = $Object->{$Prefix.'TreeRight'};
		$Node->Depth = $Object->{$Prefix.'Depth'};
		$Node->{$Prefix.'ID'} = $Object->{$Prefix.'ID'};
		$Node->ParentID = property_exists($Object, $Prefix.'ParentID') ? $Object->{$Prefix.'ParentID'} : Null;
		return $Node;
	}
}

if (!function_exists('SectionAnchor')) {
	function SectionAnchor($Node) {
		$Href = GetValue('URI', $Node);
		if (!$Href) $Href = GetValue('RequestUri', $Node);
		$Name = ($Href) ? Anchor($Node->Name, $Href) : $Node->Name;
		return $Name;
	}
}



if (!function_exists('Chunk')) {
	function Chunk($Identify, $Type = 'Textarea') {
		static $ChunkModel;
		if (is_null($ChunkModel)) $ChunkModel = new ChunkModel();
		$Data = $ChunkModel->GetID($Identify);
		if ($Data) {
			$String = Gdn_Format::To($Data->Body, $Data->Format);
			if ($Type) {
				$String = Wrap($String, 'div', array('class' => 'Editable Editable'.$Type, 'id' => 'Chunk'.$Identify));
			}
			return $String;
		}
	}
}

/*

if (!function_exists('GetUrlCode')) {
	function GetUrlCode($UrlCode, $Text) {
		$Result = Null;
		if (!$UrlCode) $UrlCode = GoogleTranslate($Text, array('To' => 'en'));
		$UrlCode = SplitString($UrlCode, '\/', array('CleanupString'));
		if (count($UrlCode) > 0) $Result = implode('/', array_filter($UrlCode));
		return $Result;
	}
}*/



















