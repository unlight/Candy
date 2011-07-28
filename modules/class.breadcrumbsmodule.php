<?php if (!defined('APPLICATION')) exit();

class BreadCrumbsModule extends MenuModule {
	
	protected $bCrumbsWrapped;
	protected $bAutoWrapCrumbs;
	
	public function __construct($Sender = '') {
		parent::__construct($Sender);
		$this->HtmlId = 'BreadCrumbs';
	}
	
	public function AssetTarget() {
		return 'BreadCrumbs';
	}
	
	public function AddLink($Group, $Text, $Url, $Permission = FALSE, $Attributes = '', $AnchorAttributes = '') {
		parent::AddLink($Group, $Text, $Url, $Permission, $Attributes, $AnchorAttributes);
	}
	
	public function SetLinks($DataSet) {
		foreach ($DataSet as $Node) {
			$Node = (object)$Node;
			$this->AddLink($Node->Name, $Node->Name, $Node->URI);
		}
	}

	
	/**
	* Adds first crumb and last crumb.
	* 
	*/
	public function WrapCrumbs($First = True, $Last = True) {
		if (!$First || !$Last) return;
		$GroupFirstItem = array();
		$GroupLastItem = array();
		if ($First != False) {
			$Home = T('Home');
			$FirstItem = array(0 => array('Text' => $Home, 'Url' => '/'));
			$GroupFirstItem = array($Home => $FirstItem);
		}
		if ($Last != False) {
			$Text = $this->_Sender->Data('Title');
			$Item = array(0 => array('Text' => $Text, 'Url' => '/'));
			$GroupLastItem = array($Text => $Item);
		}
		$this->Items = $GroupFirstItem + $this->Items + $GroupLastItem;
		$this->bCrumbsWrapped = True;
	}
	
	
	//public function AutoWrapCrumbs($Value = Null) {
	public function AutoWrapCrumbs() {
/*		if (is_null($Value)) {
			// TODO: THINK ABOUT IT
		}*/
		$this->bAutoWrapCrumbs = True;
	}
	
	public function AddCrumb($Text, $Url = '/') {
		$this->AddLink($Text, $Text, $Url);
	}
	
	public function ToString() {
		$String = '';
		$AnchorAttributes = ''; // not used yet
		$ListAttributes = ''; // not used yet
		
		if (!$this->bCrumbsWrapped && $this->bAutoWrapCrumbs) $this->WrapCrumbs();
		
		$this->FireEvent('BeforeToString');
		
		$CountItems = count($this->Items);
		if ($CountItems == 0) return $String;
		
		$LastCrumbLinked = C('Candy.Modules.BreadCrumbsLastCrumbLinked', False);
		
		$Count = 0;
		foreach ($this->Items as $GroupName => $Links) {
			foreach ($Links as $Key => $Link) {
				$Text = $GroupName;
				//$Text = ArrayValue('Text', $Link);
				++$Count;
				$Attributes = ArrayValue('Attributes', $Link, array());
				if ($Count == 1) $CssClassSuffix = 'First';
				elseif ($Count == $CountItems) $CssClassSuffix = 'Last';
				else $CssClassSuffix = '';
				$Attributes['class'] = trim($CssClassSuffix . 'Crumb ' . ArrayValue('class', $Attributes, ''));
				
				$Url = ArrayValue('Url', $Link);
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
		$String = Wrap($String, 'div', array('id' => $this->HtmlId));
		return $String;
	}
	
}






