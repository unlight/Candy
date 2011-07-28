<?php if (!defined('APPLICATION')) exit();

class ChunkModel extends Gdn_Model {
	
	public function __construct() {
		parent::__construct('Chunk');
	}
	
	
	public function GetCount($Where = False) {
		$Where['bCountQuery'] = True;
		return $this->Get($Where);
	}
	
	public function Save($PostValues, $EditingData = False) {
		ReplaceEmpty($PostValues, Null);
		$Insert = (GetValue('ChunkID', $PostValues) === False);
		if ($Insert) $this->AddUpdateFields($PostValues);
		$RowID = parent::Save($PostValues);
		return $RowID;
	}
	
	public function Get($Where = False, $Offset = False, $Limit = False, $OrderBy = False, $OrderDirection = 'desc') {
		$bCountQuery = GetValue('bCountQuery', $Where, False, True);
		if ($bCountQuery) {
			$this->SQL->Select('*', 'count', 'RowCount');
			$Offset = False;
			$Limit = False;
			$OrderBy = False;
		}
		if (GetValue('Browse', $Where, True, True) && !$bCountQuery) {
			$this->SQL
				->Select('c.ChunkID, c.Name, c.InsertUserID, c.DateInserted, c.UpdateUserID, c.DateUpdated');
		}
/*		if (GetValue('InsertAuthor', $Where, False, True) && !$bCountQuery) {
		}
		if (GetValue('UpdateAuthor', $Where, False, True) && !$bCountQuery) {
		}
		*/
		
		$this->EventArguments['bCountQuery'] = $bCountQuery;
		$this->EventArguments['Where'] =& $Where;
		$this->FireEvent('BeforeGet');
		
		if ($OrderBy !== False) $this->SQL->OrderBy($OrderBy, $OrderDirection);
		if (is_array($Where)) $this->SQL->Where($Where);
		$Result = $this->SQL->From($this->Name . ' c')->Limit($Limit, $Offset)->Get();
		if ($bCountQuery) $Result = $Result->FirstRow()->RowCount;
		return $Result;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	protected $_Cache;
	
	public static function GetChunk($Name) {
		$Result =& $this->_Cache[$Name];
		if (is_null($Result)) {
			if (file_exists(PATH_LOCAL_CONF.'/chunks/'.$Name))
				$Result = file_get_contents(PATH_LOCAL_CONF.'/chunks/'.$Name);
			
			//if (file_get_contents())
			//$Result = 
		}
	}
	
	public static function SearchPhpChunksInFile($Filepath) {
		$Content = file_get_contents($Filepath);
		return self::SearchPhpChunks($Content);
	}
	
	public static function SearchPhpChunks($Content) {
		$Tokens = token_get_all($Content);
		$Result = array();
		for ($Count = count($Tokens), $i = 0; $i < $Count; ++$i) {
			$Token =& $Tokens[$i];
			if (!(is_array($Token) && array_key_exists(1, $Token) 
				&& $Token[0] == 307 && $Token[1] == 'Chunk')) continue;
			$CloseBracePos = $OpenBracePos = FALSE;
			for ($k = $i + 1; $k < $Count; ++$k) {
				if (is_string($Tokens[$k])) {
					if ($Tokens[$k] == '(') $OpenBracePos = $k;
					elseif ($Tokens[$k] == ')') $CloseBracePos = $k;
				}
				if ($CloseBracePos !== FALSE && $OpenBracePos !== FALSE) break;
			}
			//$Garbage = array_slice($Tokens, $CloseBracePos - 2, $CloseBracePos - $OpenBracePos);
			$Garbage = array_slice($Tokens, $CloseBracePos - 1, 1);
			if (count($Garbage) != 1) trigger_error("Incorrect using of Chunk() function.", E_USER_ERROR);
			list($Garbage) = $Garbage;
			$IntToken = $Garbage[0];
			if ($IntToken == T_LNUMBER) $Result['Integer'] = $Garbage[1];
			elseif ($IntToken == T_CONSTANT_ENCAPSED_STRING) $Result['String'] = trim($Garbage[1], '"\'');
			else d('problem', $Garbage, token_name(315));
		}
		return $Result;
	}


}







