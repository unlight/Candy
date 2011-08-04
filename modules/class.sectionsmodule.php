<?php if (!defined('APPLICATION')) exit();

class SectionsModule extends Gdn_Module {
	
	public $RootNodeDepth = 0;
	
	public function __construct($Sender = '') {
		parent::__construct($Sender);
		$RootNodeID = C('Candy.RootSectionID');
		if ($RootNodeID) {
			$SectionModel = Gdn::Factory('SectionModel');
			$RootNode = $SectionModel->GetNode($RootNodeID);
			$this->RootNodeDepth = $RootNode->Depth;
		}
	}
	
	public function GetDirectChildsData($Section) {
		$SectionModel = Gdn::Factory('SectionModel');
		$Childs = $SectionModel->GetChildrens('*', $Section, array('DirectDescendants' => True));
		$this->SetData('Sections', $Childs);
		//$this->RootNodeDepth = GetValue('Depth', $Childs->FirstRow());
	}
	
	public function SetAjarData($SectionPath = False) {
		$SectionModel = Gdn::Factory('SectionModel');
		if ($SectionPath === False) $SectionPath = GetValueR($this->_Sender, 'SectionID');
		elseif (is_object($SectionPath) && $SectionPath instanceof StdClass) {
			$SectionPath = $SectionPath->SectionID;
		}
		if ($SectionPath) $this->SetData('Sections', $SectionModel->Ajar($SectionPath, '', False));
	}


	public function AssetTarget() {
		return 'Panel';
	}

	public function ToString() {
		$String = '';
		$Sections = $this->Data('Sections');
		if ($Sections) $String = parent::ToString();
		return $String;
	}
	
}
