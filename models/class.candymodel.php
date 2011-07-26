<?php if (!defined('APPLICATION')) exit();

class CandyModel {
	
	public static function Slug($Text) {
		$Result = GoogleTranslate($Text, array('To' => 'en'));
		$Result = CleanUpString($Result);
		return $Result;
	}
	
	public static function IsOwner($Object) {
		$Session = Gdn::Session();
		return ($Session->UserID > 0 && GetValue('InsertUserID', $Object) == $Session->UserID);
	}
	
	public static function AvailableApplications() {
		$Result = array();
		$Result = Gdn::Config('EnabledApplications', array());
		$Result = array_flip($Result);
		return $Result;
	}
	
	
}