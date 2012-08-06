<?php if (!defined('APPLICATION')) exit();

class SectionModel extends TreeModel {
	
	public $ParentKey = 'ParentID';
	public $PrimaryKey = 'SectionID';
	
	public function __construct($Name = False) {
		if (!$Name) $Name = C('Candy.Sections.Table', 'Section');
		parent::__construct($Name);
		$this->Validation->AddRule('UrlPath', 'function:ValidateUrlPath');
	}
	
	public function GetByReference($RowID, $Field = False) {
		if (is_numeric($RowID)) $Result = $this->GetID($RowID);
		else $Result = $this->GetByName($RowID);
		return $Result;
	}
	
	public static function GetRequestUri($Code) {
		$Result = CandyModel::GetRoute($Code);
		if (!$Result) return False;
		return $Result->RequestUri;
	}
	
	public function GetByName($Name) {
		$Result = $this->GetWhere(array('Name' => $Name))->FirstRow();
		return $Result;
	}
	
	public function GetID($RowID) {
		$Result = $this->GetWhere(array($this->PrimaryKey => $RowID))->FirstRow();
		return $Result;
	}
	
	/**
	* The path from $NodeID the root node $RootNode.
	* 
	*/
	public function GetPath($Node, $RootNode = False, $IncludeRoot = True) {
		$Where = False;
		if (is_numeric($RootNode) && $RootNode != 1) {
			list($LeftID, $RightID) = $this->NodeValues($RootNode);
			$Op = ($IncludeRoot) ? '=' : '';
			$Where['TreeLeft >'.$Op] = $LeftID;
			$Where['TreeRight <'.$Op] = $RightID;
		}
		list($LeftID, $RightID, $Depth, $NodeID) = $this->NodeValues($Node);
		$Where['TreeLeft <='] = $LeftID;
		$Where['TreeRight >='] = $RightID;
		$Result = $this->GetNodes($Where);
		return $Result;
	}
	
	public function CheckUniqueURI($Value = Null) {
		if (is_null($Value)) return True;
		if (is_array($Value)) $Value = ArrayValue('URI', $Value);
		$Data = $this->GetRequestUri($Value);
		if ($Data == False) return True;
		return False;
	}
	
	public function Save($PostValues, $PreviousValues = False) {
		ReplaceEmpty($PostValues, Null);
		
		$RowID = GetValue($this->PrimaryKey, $PostValues);
		$Insert = ($RowID === False);
		$URI = GetValue('URI', $PostValues, Null);
		if ($URI !== Null) $this->Validation->ApplyRule('URI', 'UrlPath');
		if ($Insert && !$this->CheckUniqueURI($URI)) $this->Validation->AddValidationResult('URI', '%s: Already exists.');
		
		if (GetValue('ParentID', $PostValues) === Null) SetValue('ParentID', $PostValues, 1);
		
		if (array_key_exists('Mask', $PostValues)) {
			$PostValues['Mask'] = self::CalculateMask($PostValues['Mask']);
		}
		
		$this->DefineSchema();
		$this->AddUpdateFields($PostValues);
		if ($Insert) $this->AddInsertFields($PostValues);
		
		if ($this->Validate($PostValues, $Insert) === True) {
			$Fields = $this->Validation->SchemaValidationFields();
			if ($Insert) {
				$ParentID = GetValue('ParentID', $PostValues);
				$RowID = parent::InsertNode($ParentID, $Fields);
				if (!$RowID) $this->Validation->AddValidationResult('RowID', '%s: InsertNode operation failed.');
			} else {
				$this->Update($Fields, array('SectionID' => $RowID));
			}
			if (count($this->Validation->Results()) == 0) CandyModel::SaveRoute($PostValues);
		} else {
			$RowID = False;
		}

		return $RowID;
	}
	
	protected static function CalculateMask($Mask) {
		$Mask = preg_replace('/[^0-9\.\-+\^\/\*]/', ' ', $Mask);
		$Mask = preg_replace('~([0-9]+)\^([0-9]+)~e', 'pow("\\1", "\\2")', $Mask);
		@eval("\$NewMask = (int)($Mask);");
		if (!isset($NewMask)) $NewMask = $Mask;
		return $NewMask;
	}

}












