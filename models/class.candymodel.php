<?php if (!defined('APPLICATION')) exit();

class CandyModel {
	
	/**
	* Save mask information.
	* 
	* @param array $PostValues 
	* @param mixed $Validation.
	*/
	public static function SaveMaskInfo($PostValues, $Validation = False) {
		$MaskValues = array();
		foreach ($PostValues as $Key => $Value) {
			if (StringBeginsWith($Key, 'Mask_')) {
				$MaskValues[$Value] = GetValue('Description_'.$Value, $PostValues);
			}
		}
		$NewMaskValues = array_combine($PostValues['Mask'], $PostValues['Description']);
		$MaskValues = $MaskValues + $NewMaskValues;

		$Data = array();
		foreach ($MaskValues as $Int => $Description) {
			$Int = sprintf('%u', $Int);
			if ($Int > 0 && !($Int & ($Int-1))) $Data[$Int] = $Description;
		}
		K('Candy.Mask.Info', $Data);
		return True;
	}
	
	public static function Slug($Text) {
		$Result = GoogleTranslate($Text, array('To' => 'en'));
		$Result = CleanUpString($Result);
		return $Result;
	}
	
	public static function GetRoutes() {
		$Result = Gdn::SQL()
			->From('Route')
			->Get();
		return $Result;
	}
	
	public static function GetRouteRequestUri($URI) {
		$Route = self::GetRoute($URI);
		return GetValue('RequestUri', $Route);
	}
	
	public static function GetRoute($URI) {
		$Result = Gdn::SQL()
			->Select('*')
			->From('Route')
			->Where('URI', $URI)
			->Get()
			->FirstRow();
		return $Result;
	}
	
	public static function SaveRoute($URI, $RequestUri = Null) {
		if (is_array($URI) || is_object($URI)) {
			$RequestUri = GetValue('RequestUri', $URI);
			$URI = GetValue('URI', $URI);
		}
		if ($URI !== Null) {
			$SQL = Gdn::SQL();
			$SQL->Replace('Route', array('RequestUri' => $RequestUri), array('URI' => $URI));
		}
	}
	
	public static function DeleteRoute($URI, $RequestUri = Null) {
		$SQL = Gdn::SQL();
		$Where = array();
		if ($RequestUri !== Null) $Where['RequestUri'] = $RequestUri;
		else $Where = array('URI' => $URI);
		$SQL->Delete('Route', $Where);
	}
	
/*	public static function IsOwner($Object, $HasAccessPermission = False) {
		//if C('Debug') Deprecated(__METHOD__, 'IsContentOwner');
		$Session = Gdn::Session();
		if (is_string($HasAccessPermission)) {
			$HasAccessPermission = $Session->CheckPermission($HasAccessPermission);
		}
		return $HasAccessPermission || ($Session->UserID > 0 && GetValue('InsertUserID', $Object) == $Session->UserID);
	}*/
	
/*	public static function AvailableApplications() {
		$Result = array();
		$Result = Gdn::Config('EnabledApplications', array());
		$Result = array_flip($Result);
		return $Result;
	}*/
	
	
	
}

