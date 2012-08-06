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

		if ($this->Head) {
			SetMetaTags($Page, $this);
			if ($Page->CustomCss) {
				$CustomCss = "\n" . $Page->CustomCss;
				if (!StringBeginsWith(trim($CustomCss), '<style', True)) $CustomCss = Wrap($CustomCss, 'style', array('type' => 'text/css'));
				$this->Head->AddString($CustomCss);
			}
			if ($Page->CustomJs) {
				$CustomJs = $Page->CustomJs;
				if (!StringBeginsWith(trim($CustomJs), '<script', True)) $CustomJs = Wrap($CustomJs, 'script', array('type' => 'text/javascript'));
				$this->Head->AddString($CustomJs);
			}
		}
		
		if ($Page->SectionID) {
			$this->Section = BuildNode($Page, 'Section');
			$this->SectionID = $Page->SectionID;
			CandyHooks::AddModules($this, $this->Section);
		}

		$this->FireEvent('ContentPage');
		
		if ($Page->View) $this->View = $this->FetchViewLocation($this->View, False, False, False);
		if (!$this->View) {
			$this->View = $this->FetchViewLocation('view', 'page', '', False);
			if (!$this->View) $this->View = 'default';
		}
		
		$this->Title($Page->Title);
		$this->SetData('Content', $Page, True);
		
		$this->EventArguments['Format'] =& $Page->Format;
		$this->EventArguments['Body'] =& $Page->Body;
		$this->FireEvent('BeforeBodyFormat');
		$this->ContentBodyHtml = Gdn_Format::To($Page->Body, $Page->Format);
		
		$Doc = PqDocument($this->ContentBodyHtml);
		$Header = $Doc->Find('h1');
		$CountH1 = count($Header);
		if ($CountH1 == 0) $this->SetData('Headline', Gdn_Format::Text($Page->Title));
		elseif ($CountH1 == 1) {
			$this->SetData('Headline', $Header->Text());
			$Header->Remove();
			$this->ContentBodyHtml = $Doc->Html();
		}
		// 
		
		$this->AddModule('PageInfoModule');
		$this->Render();
	}
	
	public function Map() {
		$this->Title(T('Site map'));
		$SectionModel = Gdn::Factory('SectionModel');
		$IncludeRoot = False;
		$this->Tree = $SectionModel->GetNodes(array('IncludeRoot' => $IncludeRoot));
		$this->AddHomeTreeNode = !$IncludeRoot;
		$BreadCrumbs = new BreadCrumbsModule($this);
		$BreadCrumbs->AutoWrapCrumbs();
		$this->AddModule($BreadCrumbs);
		$this->Render();
	}

}

