<?php if (!defined('APPLICATION')) exit();

class SectionModel extends TreeModel {
	
	public $ParentKey = 'ParentID';
	public $PrimaryKey = 'SectionID';
	
	public function __construct() {
		parent::__construct('Section');
	}
	
	public function GetByReference($RowID, $Field = False) {
		if (is_numeric($RowID)) $Result = $this->GetID($RowID);
		else $Result = $this->GetByName($RowID);
		return $Result;
	}
	
	public function GetByURI($Code) {
		$Result = $this->GetWhere(array('URI' => $Code))->FirstRow();
		return $Result;
	}
	
	public function GetByName($Name) {
		$Result = $this->GetWhere(array('Name' => $Name))->FirstRow();
		return $Result;
	}
	
	public function GetID($RowID) {
		$Result = $this->GetWhere(array($this->PrimaryKey => $RowID))->FirstRow();
		return $Result;
	}
	
	public function Full($Fields = '', $Where = False, $RootID = False, $IncludeRoot = False) {
		if (is_numeric($RootID)) {
			list($LeftID, $RightID, $Depth, $NodeID) = $this->_NodeValues($RootID);
			$Op = ($IncludeRoot) ? '=' : '';
			$Where['TreeLeft >'.$Op] = $LeftID;
			$Where['TreeRight <'.$Op] = $RightID;
		}
		$Result = parent::Full($Fields, $Where);
		return $Result;
	}
	
	public function Save($PostValues, $PreviousValues = False) {
		ReplaceEmpty($PostValues, Null);
		$URI = GetValue('URI', $PostValues, Null);
		if ($URI !== Null) $this->Validation->ApplyRule('URI', array('Required', 'UrlString'));
		
		// TEST
		//if ($URI === Null) $PostValues['URI'] = CandyModel::Slug($PostValues['Name']);
		
		$RowID = GetValue($this->PrimaryKey, $PostValues);
		$Insert = ($RowID === False);
		if (GetValue('ParentID', $PostValues, Null) === Null) SetValue('ParentID', $PostValues, 1);
		
		$this->DefineSchema();
		$this->AddUpdateFields($PostValues);
		if ($Insert) $this->AddInsertFields($PostValues);
		
		if ($this->Validate($PostValues, $Insert) === True) {
			$Fields = $this->Validation->ValidationFields();
			if ($Insert) {
				$ParentID = GetValue('ParentID', $PostValues);
				$RowID = parent::InsertNode($ParentID, $Fields);
				if (!$RowID) $this->Validation->AddValidationResult('RowID', '%s: InsertNode operation failed.');
			} else {
				$this->Update($Fields, array('SectionID' => $RowID));
			}
		} else {
			return False;
		}
		
		$RowID = (count($this->ValidationResults()) == 0) ? $RowID : False;
		return $RowID;
	}
		
}












