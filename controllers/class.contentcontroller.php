<?php if (!defined('APPLICATION')) exit();

class ContentController extends Gdn_Controller {
	
	public $Uses = array('Form', 'SectionModel');
	
	public $SectionID;
	public $Section;
	
	public function Initialize() {
		if ($this->DeliveryType() == DELIVERY_TYPE_ALL) {
			$this->Head = new HeadModule($this);
			$this->AddJsFile('jquery.js');
			$this->AddJsFile('jquery.livequery.js');
			$this->AddJsFile('global.js');
			$this->AddCssFile('style.css');
			$this->AddCssFile('candy.css');
			//$this->AddSideMenu();
			$this->AddJsFile('candy.js'); // Application global js
		}
		parent::Initialize();
	}
	
	public function AddSideMenu($CurrentUrl = '') {
		// Only add to the assets if this is not a view-only request
		if ($this->_DeliveryType == DELIVERY_TYPE_ALL) {
			$SideMenu = new SideMenuModule($this);
			$SideMenu->HtmlId = 'ContentSideMenu';
			$SideMenu->HighlightRoute($CurrentUrl);
			//$SideMenu->Sort = C('Garden.DashboardMenu.Sort');
			$this->EventArguments['SideMenu'] =& $SideMenu;
			$this->FireEvent('AfterAddSideMenu');
			$this->AddModule($SideMenu, 'Panel');
		}
	}
	
	public $Page;
	
	public function Page($Reference) {
		$PageModel = new PageModel();
		$Page = $PageModel->GetFullID($Reference);
		if (!$Page) throw NotFoundException();
		$this->Page = $Page;
		
		if ($Page->SectionID) {
			$this->Section = BuildNode($Page, 'Section');
			$this->SectionID = $Page->SectionID;
			$this->SectionPath = $this->SectionModel->GetPath($Page->SectionID, C('Candy.RootSectionID'), True);
			$SectionsModule = new SectionsModule($this);
			$SectionsModule->SetAjarData($this->SectionPath);
			$this->AddModule($SectionsModule);

			$BreadCrumbsModule = new BreadCrumbsModule($this);
			$BreadCrumbsModule->SetLinks($this->SectionPath);
			$this->AddModule($BreadCrumbsModule);
		}
		
		$this->AddModule('PageInfoModule');
		
		if ($Page->View) $this->View = $this->FetchViewLocation($this->View, False, False, False);
		if (!$this->View) {
			$this->View = $this->FetchViewLocation('view', 'page', '', False);
			if (!$this->View) $this->View = 'default';
		}
		
		$this->Title($Page->Title);
		$this->SetData('Content', $Page, True);
		
		$this->FireEvent('BeforePageRender');
		
		$this->Render();
	}
	
	public function Map() {
		// TODO: SET ROUTE /map
		$this->Title(T('Site map'));
		$SectionModel = Gdn::Factory('SectionModel');
		$IncludeRoot = False;
		$this->Tree = $SectionModel->Full('*', '', C('Candy.RootSectionID'), $IncludeRoot);
		
		$this->AddHomeTreeNode = !$IncludeRoot;
		
		$BreadCrumbs = new BreadCrumbsModule($this);
		$BreadCrumbs->AutoWrapCrumbs();
		$this->AddModule($BreadCrumbs);
		
		$this->Render();
	}
	
/*	public function xRender($View = '', $ControllerName = FALSE, $ApplicationFolder = FALSE, $AssetName = 'Content') {
		$this->FireEvent('BeforeContentRender');
		return parent::xRender($View, $ControllerName, $ApplicationFolder, $AssetName);
	}*/
	

	
}





