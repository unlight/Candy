<?php if (!defined('APPLICATION')) exit();

Gdn::FactoryInstall('SectionModel', 'SectionModel', 
	PATH_APPLICATIONS.'/candy/models/class.sectionmodel.php', Gdn::FactorySingleton);
	
if (!function_exists('SetMetaTags')) {
	function SetMetaTags($Page, $Controller = Null) {
		if (!$Controller) $Controller = Gdn::Controller();
		if ($Page->MetaDescription) $Controller->Head->AddTag('meta', array('name' => 'description', 'content' => $Page->MetaDescription, '_sort' => 0));
		if ($Page->MetaKeywords) $Controller->Head->AddTag('meta', array('name' => 'keywords', 'content' => $Page->MetaKeywords, '_sort' => 0));
		if ($Page->MetaRobots) $Controller->Head->AddTag('meta', array('name' => 'robots', 'content' => $Page->MetaKeywords, '_sort' => 0));
		if ($Page->MetaTitle) $Controller->Head->Title($Page->MetaTitle);
		$Controller->Head->AddTag('meta', array('http-equiv' => 'content-language', 'content' => Gdn::Locale()->Current()));
		$Controller->Head->AddTag('meta', array('http-equiv' => 'content-type', 'content' => 'text/html; charset=utf-8'));
	}
}
	
if (!function_exists('ValidateUrlPath')) {
	function ValidateUrlPath($Value, $Field = '') {
		return ValidateRegex($Value, '/^([\/\d\w\-]+)?$/');
	}
}

if (!function_exists('IsContentOwner')) {
	function IsContentOwner($Object, $HasAccessPermission = False) {
		$Session = Gdn::Session();
		if (is_string($HasAccessPermission)) {
			$HasAccessPermission = $Session->CheckPermission($HasAccessPermission);
		}
		return $HasAccessPermission || ($Session->UserID > 0 && GetValue('InsertUserID', $Object) == $Session->UserID);
	}
}


if (!function_exists('BuildNode')) {
	/**
	* BuildNode($Object, 'Section')
	* 
	*/
	function BuildNode($Object, $Prefix) {
		$Node = new StdClass();
		$Node->TreeLeft = $Object->{$Prefix.'TreeLeft'};
		$Node->TreeRight = $Object->{$Prefix.'TreeRight'};
		$Node->Depth = $Object->{$Prefix.'Depth'};
		$Node->{$Prefix.'ID'} = $Object->{$Prefix.'ID'};
		$Node->ParentID = property_exists($Object, $Prefix.'ParentID') ? $Object->{$Prefix.'ParentID'} : Null;
		return $Node;
	}
}

if (!function_exists('SectionAnchor')) {
	function SectionAnchor($Node, $Attributes = Null) {
		$Url = GetValue('Url', $Node);
		if (!$Url) {
			$Url = GetValue('URI', $Node);
			if (!$Url) GetValue('RequestUri', $Node);
		}
		
		$NoFollowExternal = GetValue('NoFollowExternal', $Attributes, False, True);
		if ($NoFollowExternal) {
			
			static $OurHost; if (is_null($OurHost)) $OurHost = Gdn::Request()->Host();
			$UrlHost = parse_url($Url, PHP_URL_HOST);
			if ($UrlHost && $UrlHost != $OurHost) {
				if (isset($Attributes['rel'])) $Attributes['rel'] .= ' nofollow';
				else $Attributes['rel'] = 'nofollow';
			}
		}
		
		$Name = ($Url) ? Anchor($Node->Name, $Url, '', $Attributes) : $Node->Name;
		return $Name;
	}
}



if (!function_exists('Chunk')) {
	function Chunk($Identify, $Options = False) {
		static $ChunkModel; if (is_null($ChunkModel)) $ChunkModel = new ChunkModel();
		static $PermissionChunksEdit; if (is_null($PermissionChunksEdit)) $PermissionChunksEdit = CheckPermission('Candy.Chunks.Edit');
		$Data = $ChunkModel->GetID($Identify);
		if ($Data != False) {
			$String = Gdn_Format::To($Data->Body, $Data->Format);
			$Type = ArrayValueI('type', $Options, 'Textarea');
			$Class = ArrayValueI('class', $Options, '');
			if ($Type) {
				if ($PermissionChunksEdit) $Class .= ' Editable Editable'.$Type;
				$String = Wrap($String, 'div', array('class' => trim($Class), 'id' => 'Chunk'.$Data->ChunkID));
			}
			return $String;
		}
	}
}






