<?php if (!defined('APPLICATION')) exit();

class SectionsModule extends Gdn_Module {
	
	public $RootNodeDepth = 0;
	public $View = '';
	
	public function FetchViewLocation($View = '', $ApplicationFolder = '') {
		if ($this->View != '') $View = $this->View;
		return parent::FetchViewLocation($View, $ApplicationFolder);
	}
	
	public function __construct($Sender = '', $ApplicationFolder = False) {
		parent::__construct($Sender, $ApplicationFolder);
	}
	
	public function GetSiblingsData($Section) {
		$SectionModel = Gdn::Factory('SectionModel');
		$Items = $SectionModel->GetSiblings('*', $Section);
		$this->SetData('Items', $Items);
		return $Items;
	}
	
	public function GetDirectChildsData($Section) {
		$SectionModel = Gdn::Factory('SectionModel');
		$Childs = $SectionModel->GetChildrens('*', $Section, array('DirectDescendants' => True));
		$this->SetData('Items', $Childs);
		//$this->RootNodeDepth = GetValue('Depth', $Childs->FirstRow());
		return $Childs;
	}
	
	public function SetAjarData($SectionPath = False) {
		$SectionModel = Gdn::Factory('SectionModel');
		if ($SectionPath === False) $SectionPath = GetValueR('SectionID', $this->_Sender);
		elseif (is_object($SectionPath) && $SectionPath instanceof StdClass) {
			$SectionPath = $SectionPath->SectionID;
		}
		if ($SectionPath) $this->SetData('Items', $SectionModel->Ajar($SectionPath, '', False));
	}


	public function AssetTarget() {
		return 'Panel';
	}

	public function ToString() {
		$String = '';
		$Sections = $this->Data('Items');
		if ($Sections && (is_array($Sections) && count($Sections) > 0) || ($Sections instanceof Gdn_DataSet && $Sections->NumRows() > 0)) {
			$String = parent::ToString();
		}
		return $String;
	}
	
}
