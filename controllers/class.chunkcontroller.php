<?php if (!defined('APPLICATION')) exit();

class ChunkController extends CandyController {
	
	public $Uses = array('Form', 'ChunkModel');
	public $Editing;
	protected $AdminView = True;
	
	public function Initialize() {
		parent::Initialize();
		$this->Permission('Garden.Admin.Only'); // TODO: SET REAL PERMISSIONS
		if ($this->DeliveryType() == DELIVERY_TYPE_ALL) {
			$this->AddSideMenu();
			//$this->AddCssFile('candy.css');
		}
	}
	
	public function Browse($Page = '') {
		list($Offset, $Limit) = OffsetLimit($Page, 30);
		$this->Chunks = $this->ChunkModel->Get('', $Offset, $Limit);
		$this->RecordCount = $this->ChunkModel->GetCount();
		$this->Url = '/candy/chunks/browse/%s';
		$this->Pager = new PagerModule($this);
		$this->Pager->Configure($Offset, $Limit, $this->RecordCount, $this->Url);
		$this->Title(T('Chunks'));
		$this->Render();
	}
	
	public function Update($Reference = '') {
		$Content = False;		
		$this->AddJsFile('jquery.textpandable.js');
		$this->AddJsFile('editform.js');
		$this->Form->SetModel($this->ChunkModel);
		
		if ($Reference != '') {
			$Content = $this->ChunkModel->GetID($Reference);
			if (!CandyModel::IsOwner($Content)) $this->Permission('Candy.Chunk.Edit');
			if ($Content) {
				$this->Form->AddHidden('ChunkID', $Content->ChunkID);
				$this->Editing = True;
				$this->Form->SetData($Content);
			}
		}
		$this->Permission('Candy.Chunk.Add');
		
		if ($this->Form->AuthenticatedPostBack()) {
			$SavedID = $this->Form->Save($Content);
			if ($SavedID) {
				$Message = T('Saved');
				$this->InformMessage($Message, array('Sprite' => 'Check', 'CssClass' => 'Dismissable AutoDismiss'));
			}
		} else {
			$this->Form->SetData($Content);
		}
		
		$this->Title(ConcatSep(' - ', T('Chunk'), GetValue('Name', $Content)));
		$this->Render();
	}
	
	public function Delete($Reference) {
		if ($this->DeliveryType() == DELIVERY_TYPE_ALL) {
			Redirect('/candy/page/browse');
		}
	}
	
}



