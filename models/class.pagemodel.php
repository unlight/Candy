<?php if (!defined('APPLICATION')) exit();

class PageModel extends Gdn_Model {
	
	public $PrimaryKey = 'PageID';
	
	public function __construct() {
		parent::__construct('Page');
		//$this->AddRule('UrlString', 'function:ValidateUrlString');
	}
	
	public function Save($PostValues, $PreviousValues = False) {
		ReplaceEmpty($PostValues, Null);
		$SectionID = GetValue('SectionID', $PostValues, Null);
		$bCreateSection = GetValue('CreateSection', $PostValues);
		if ($bCreateSection) {
			$this->Validation->ApplyRule('SectionURI', 'UrlString');
			$this->Validation->ApplyRule('SectionID', 'Required');
			unset($PostValues['SectionID']);
		}
		
		$RowID = GetValue('PageID', $PostValues);
		$Insert = ($RowID === False);
		$this->EventArguments['PostValues'] =& $PostValues;
		$this->FireEvent('BeforeSave');
		$RowID = parent::Save($PostValues);
		if ($RowID) {
			if ($bCreateSection) {
				$SectionModel = Gdn::Factory('SectionModel');
				$NodeFields = array(
					'Name' => $PostValues['Title'],
					'URI' => $PostValues['SectionURI'],
					'RequestUri' => 'content/page/'.$RowID
				);
				// $SectionID is parent node.
				$NewSectionID = $SectionModel->InsertNode($SectionID, $NodeFields);
				$this->SQL
					->Update($this->Name)
					->Set('SectionID', $NewSectionID)
					->Where($this->PrimaryKey, $RowID)
					->Put();
			}
		}

		return $RowID;
	}
	
	public function SetProperty($RowID, $Field, $Value) {
		parent::SetProperty($RowID, $Field, $Value);
		// TODO: After save.
	}
	
	public function GetCount($Where = False) {
		$Where['bCountQuery'] = True;
		$Result = $this->Get($Where);
		return $Result;
	}
	
	public function Get($Where = False, $Offset = False, $Limit = False, $OrderBy = 'p.PageID', $OrderDirection = 'desc') {
		$bCountQuery = GetValue('bCountQuery', $Where, False, True);
		if ($bCountQuery) {
			$this->SQL->Select('*', 'count', 'RowCount');
			$Offset = $Limit = False;
		}
		if (GetValue('Browse', $Where, True, True) && !$bCountQuery) {
			$this->SQL
				->Select('p.PageID, p.Title, p.Visible, p.SectionID')
				->Select('p.DateInserted, p.DateInserted, p.UpdateUserID, p.DateUpdated');
		}
		if ($Join = GetValue('WithSection', $Where, False, True)) {
			if (!in_array($Join, array('left', 'inner'), True)) $Join = 'left';
			$this->SQL
				->Join('Section s', 's.SectionID = p.SectionID', $Join);
			if (!$bCountQuery) {
				$this->SQL
					->Select('s.SectionID as SectionID')
					->Select('s.TreeLeft as SectionTreeLeft')
					->Select('s.TreeRight as SectionTreeRight')
					->Select('s.Depth as SectionDepth')
					->Select('s.ParentID as SectionParentID')
					->Select('s.Name as SectionName')
					->Select('s.URI as SectionURI')
					->Select('s.RequestUri as SectionRequestUri');
			}
		}
		
		$this->EventArguments['bCountQuery'] = $bCountQuery;
		$this->EventArguments['Where'] =& $Where;
		$this->FireEvent('BeforeGet');
		
		if ($OrderBy !== False && !$bCountQuery) $this->SQL->OrderBy($OrderBy, $OrderDirection);
		if (is_array($Where)) $this->SQL->Where($Where);
		$Result = $this->SQL->From('Page p')->Limit($Limit, $Offset)->Get();
		if ($bCountQuery) $Result = $Result->FirstRow()->RowCount;
		return $Result;
	}
	
	public function GetFullID($PageID) {
		$this->SQL->Select('p.*');
		$Where = array('p.PageID' => $PageID, 'WithSection' => True);
		$DataSet = $this->Get($Where, False, False, False, False);
		$Result = $DataSet->FirstRow();
		return $Result;
	}
	
	public function Delete($PageID) {
		$this->SQL
			->Where('PageID', $PageID)
			->Delete($this->Name);
	}
	
}












