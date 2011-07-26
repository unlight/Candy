<?php if (!defined('APPLICATION')) exit();

class PageController extends CandyController {
	
	public $Uses = array('Form', 'PageModel');
	public $Editing;
	
	public function Initialize() {
		parent::Initialize();
		$this->Permission('Garden.Admin.Only'); // TODO: SET REAL PERMISSIONS
		if ($this->DeliveryType() == DELIVERY_TYPE_ALL) {
			$this->AddSideMenu();
			$this->AddCssFile('candy.css');
		}
	}
	
	public function Browse($Page = '') {
		list($Offset, $Limit) = OffsetLimit($Page, 30);
		$this->Pages = $this->PageModel->Get('', $Offset, $Limit);
		$this->RecordCount = $this->PageModel->GetCount();
		$this->Url = '/candy/page/browse/%s';
		$this->Pager = new PagerModule($this);
		$this->Pager->Configure($Offset, $Limit, $this->RecordCount, $this->Url);
		$this->Title(T('Pages'));
		$this->Render();
	}
	
	public function AddNew() {
		$this->View = 'Edit';
		$this->Edit();
	}
	
	public function Visible($PageID) {
		$Page = $this->PageModel->GetID($PageID);
		if ($this->Form->IsPostBack()) {
			$Visible = ForceBool($Page->Visible, 0, 0, 1); // Invert Visible property.
			$this->PageModel->SetProperty($Page->PageID, 'Visible', $Visible);
			$Page = $this->PageModel->GetID($PageID); // Get just updated content.
			if ($this->DeliveryType() == DELIVERY_TYPE_ALL) {
				$Target = GetIncomingValue('Target', '/candy/page/browse');
				Redirect($Target);
			}
			$this->SetData('Content', $Page);
			$PageInfoModule = new PageInfoModule($this);
			$this->JsonTarget('#PageInfoModule', $PageInfoModule->ToString(), 'Html');
		} else {
			$this->Form->SetData($Page);
		}
		$this->Render();
	}
	
	public function Edit($Reference = '') {
		
		//$this->AddJsFile('jquery.autocomplete.pack.js');
		$this->AddJsFile('jquery.textpandable.js');
		$this->AddJsFile('editform.js');
		$this->Form->SetModel($this->PageModel);
		
		$SectionModel = new SectionModel();
		$this->Tree = $SectionModel->DropDownArray('Name', $SectionModel->Full('', array('Depth >' => 0)));
		
		$Content = False;
		if ($Reference != '') {
			$Content = $this->PageModel->GetID($Reference);
			if (!CandyModel::IsOwner($Content)) $this->Permission('Candy.Content.Edit');
			$this->Form->AddHidden('PageID', $Content->PageID);
			if ($Content) {
				$this->Form->SetData($Content);
				$this->Editing = True;
			}
		}
		$this->Permission('Candy.Content.Add');
		
		if ($this->Form->AuthenticatedPostBack()) {
			if ($this->Form->ButtonExists('Delete')) {
				$this->PageModel->Delete($Content->PageID);
				$this->InformMessage(T('Page deleted'), array('Sprite' => 'SkullBones', 'CssClass' => 'Dismissable AutoDismiss'));
			} elseif ($this->Form->Save($Content) != False) {
				$this->InformMessage(T('Saved'), array('Sprite' => 'Check', 'CssClass' => 'Dismissable AutoDismiss'));
			}
		}
		
		$this->Title(ConcatSep(' - ', T('Page'), GetValue('Title', $Content)));
		$this->Render();
	}
	
	public function Delete($Reference) {
		$this->PageModel->Delete($Reference);
		if ($this->DeliveryType() == DELIVERY_TYPE_ALL) {
			Redirect('/candy/page/browse');
		}
	}
	
}



