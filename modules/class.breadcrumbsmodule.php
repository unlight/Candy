<?php if (!defined('APPLICATION')) exit();

class BreadCrumbsModule extends MenuModule {
	
	protected $bCrumbsWrapped;
	protected $bAutoWrapCrumbs;
	protected $Controller;
	//public $bCustomAssetTarget;
	
	public function __construct($Sender = '', $ApplicationFolder = False) {
		parent::__construct($Sender, $ApplicationFolder);
		$this->HtmlId = 'BreadCrumbs';
		//if (is_object($Sender)) $this->Controller =& $Sender;
		//else $this->Controller = GetValueR('EventArguments.Controller', Gdn::Dispatcher());
	}
	
	public function AssetTarget() {
		return 'BreadCrumbs';
	}
	
	public function AddLink($Group, $Text, $Url, $Permission = False, $Attributes = '', $AnchorAttributes = '') {
		parent::AddLink($Group, $Text, $Url, $Permission, $Attributes, $AnchorAttributes);
	}
	
	public function SetLinks($DataSet) {
		$RootNodeID = 1;
		foreach ($DataSet as $Node) {
			$Node = (object)$Node;
			$Attributes = '';
			if ($RootNodeID == $Node->SectionID) $Attributes['_HomeLink'] = True;
			$Url = $Node->Url;
			if (!$Url) $Url = $Node->RequestUri;
			$this->AddLink($Node->Name, $Node->Name, $Url, False, $Attributes);
		}
	}

	
	/**
	* Adds first crumb and last crumb.
	* 
	*/
	public function WrapCrumbs($First = True, $Last = True) {
		if (!($First && $Last)) return;
		$GroupFirstItem = array();
		$GroupLastItem = array();
		if ($First != False) {
			$Home = T('Home');
			$FirstItem = array(0 => array('Text' => $Home, 'Url' => '/'));
			$GroupFirstItem = array($Home => $FirstItem);
		}
		if ($Last != False) {
			$Text = Gdn::Controller()->Data('Title');
			//$this->Controller
			//$Text = $this->_Sender->Data('Title');
			$Item = array(0 => array('Text' => $Text, 'Url' => '/'));
			$GroupLastItem = array($Text => $Item);
		}
		$this->Items = $GroupFirstItem + $this->Items + $GroupLastItem;
		$this->bCrumbsWrapped = True;
	}
	
	public function AutoWrapCrumbs($Value = Null) {
		$this->bAutoWrapCrumbs = True;
	}
	
	public function AddCrumb($Text, $Url = '/') {
		$this->AddLink($Text, $Text, $Url);
	}
	
	public function ToString() {
		$String = '';
		
		if (!$this->bCrumbsWrapped && $this->bAutoWrapCrumbs) $this->WrapCrumbs();
		
		$this->FireEvent('BeforeToString');
		
		$CountItems = count($this->Items);
		if ($CountItems == 0) return $String;
		
		$LastCrumbLinked = C('Candy.Modules.BreadCrumbsLastCrumbLinked', False);
		
		$Count = 0;
		foreach ($this->Items as $GroupName => $Links) {
			foreach ($Links as $Key => $Link) {
				$AnchorAttributes = array(); // not used yet
				$ListAttributes = array();
				$Text = $GroupName;
				//$Text = ArrayValue('Text', $Link);
				$Count = $Count + 1;
				$Attributes = ArrayValue('Attributes', $Link, array());
				if ($Count == 1) {
					$CssClassSuffix = 'First';
					$ListAttributes['class'] = 'BreadCrumbs';
				}
				elseif ($Count == $CountItems) $CssClassSuffix = 'Last';
				else $CssClassSuffix = '';
				$Attributes['class'] = trim($CssClassSuffix . 'Crumb ' . ArrayValue('class', $Attributes, ''));

				$Url = ArrayValue('Url', $Link);
				if ($Url === NULL) {
					$IsHomeLink = GetValue('_HomeLink', $Attributes, False, True);
					if ($IsHomeLink) $Url = '/';
				}
				if ($Url) $AnchorAttributes['href'] = Url($Url, True);
				
				$Anchor = '<a'.Attribute($AnchorAttributes).'>'.$Text.'</a>';
				
				if ($Count == $CountItems) {
					if ($LastCrumbLinked) $Text = $Anchor;
					$Item = '<li'.Attribute($Attributes).'>'.$Text.'</li>';
				} else {
					$Item = '<li'.Attribute($Attributes).'>'.$Anchor;
				}
				
				$String .= str_repeat("\t", $Count);
				$String .= "\n<ul".Attribute($ListAttributes).'>'.$Item;
			}
		}
		$String .= str_repeat("\n</ul></li>", $Count - 1) . '</ul>';
		//if (!$this->bCustomAssetTarget) $String = Wrap($String, 'div', array('id' => $this->HtmlId));
		$String = Wrap($String, 'div', array('id' => $this->HtmlId));
		return $String;
	}
	
}






