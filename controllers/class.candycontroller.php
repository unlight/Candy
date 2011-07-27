<?php if (!defined('APPLICATION')) exit();

class CandyController extends Gdn_Controller {
	
	protected $AdminView;
	
	public function Initialize() {
		if ($this->DeliveryType() == DELIVERY_TYPE_ALL) {
			$this->Head = new HeadModule($this);
			$this->AddJsFile('jquery.js');
			$this->AddJsFile('jquery.livequery.js');
			$this->AddJsFile('jquery.menu.js');
			$this->AddJsFile('global.js');
			if ($this->AdminView) {
				$this->MasterView = 'admin';
				$this->AddCssFile('admin.css');
			} else {
				$this->AddCssFile('style.css');
			}
			$this->AddCssFile('candy.css');
			$this->AddJsFile('candy.js'); // Application global js
		}
		parent::Initialize();
	}
	
	public function AddSideMenu($CurrentUrl = '') {
		if ($this->_DeliveryType == DELIVERY_TYPE_ALL) {
			$SideMenu = new SideMenuModule($this);
			//$SideMenu->HtmlId = 'CandySideMenu';
			$SideMenu->CssClass = 'CandySideMenu';
			$SideMenu->HighlightRoute($CurrentUrl);
			$SideMenu->Sort = C('Garden.DashboardMenu.Sort');
			$this->EventArguments['SideMenu'] =& $SideMenu;
			$this->FireEvent('GetAppSettingsMenuItems');
			$this->AddModule($SideMenu, 'Panel');
		}
	}
	
	public function Slug() {
		$Text = GetIncomingValue('Text');
		echo CandyModel::Slug($Text);
	}
	
/*	public function SectionName() {
		$Session = Gdn::Session();
		if ($Session->UserID <= 0) return;
		$DataSet = Gdn::SQL()
			->Select('ContentID, Name')
			->From('ContentTree')
			->Like('Name', GetIncomingValue('q'), 'right')
			->OrderBy('Name')
			->Get();
		foreach ($DataSet as $Data) echo $Data->Name . "\n";
	}*/
	
	
/*	public static function WriteTree($Options = False) {
		if ($Options instanceof Gdn_DataSet) $Options = array('Tree' => $Options);
		$Class = ArrayValue('Class', $Options, 'Tree');
		$Tree = ArrayValue('Tree', $Options, 'Tree');
			
		echo "\n<ol class='Tree'>";
		// http://stackoverflow.com/questions/1310649/getting-a-modified-preorder-tree-traversal-model-nested-set-into-a-ul/#1790201
		$CurrentDepth = 0;
		$Counter = 0;
		foreach ($Tree as $Node) {
			
			if ($Node->Depth > $CurrentDepth) echo "<ul>";
			elseif ($Node->Depth < $CurrentDepth) {
				echo str_repeat("</li></ul>", $CurrentDepth - $Node->Depth), '</li>';
			} else {
				if ($Counter > 0) echo "</li>";
			}
			
			$CurrentDepth = $Node->Depth;
			++$Counter;
			
			$ItemAttribute = array('id' => 'Tree_'.$Node->TreeID);
			if ($Node->Depth < 2) $ItemAttribute['class'] = 'Open';
			
			$Options = array();
			$Options[] = Anchor('Add', 'candy/tree/add/'.$Node->TreeID, '');
			
			if ($Node->Depth == 0) {
				// This is root
				//$Options[] = Anchor('Properties', 'candy/content/properties/'.$Node->ContentID, '');
			} else {
				$Options[] = Anchor('Edit', 'candy/content/edit/'.$Node->ContentID, '');
				$Options[] = Anchor('Swap', 'candy/content/swap/'.$Node->ContentID, '');
				$Options[] = Anchor('Move', 'candy/content/move/'.$Node->ContentID, '');
				$Options[] = Anchor('Delete', 'candy/content/delete/'.$Node->ContentID, 'PopupConfirm');
				$Options[] = Anchor('Properties', 'candy/content/properties/'.$Node->ContentID, '');
			}
			
			echo "\n<li".Attribute($ItemAttribute).'>';
			echo '<div>';
			echo '<a href="javascript:;">'.$Node->Name.'</a>';
			echo ' ' . Wrap(implode(', ', $Options), 'span', array('class' => 'Options'));
			echo '</div>';
		}
		echo str_repeat("</li></ul>", $Node->Depth) . '</li>';	
		echo "</ol>";
	}*/
}





