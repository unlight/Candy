<?php if (!defined('APPLICATION')) exit();

class BreadCrumbsModule extends MenuModule {
	
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
	
	public function ToString() {
		$String = '';
		$Attributes = '';
		$AnchorAttributes = '';
		
		$this->FireEvent('BeforeToString');
		
		$CountItems = count($this->Items);
		if ($CountItems == 0) return $String;
		
		$Count = 0;
		foreach ($this->Items as $GroupName => $Links) {
			foreach ($Links as $Key => $Link) {
				$Url = ArrayValue('Url', $Link);
				$Url = Url($Url, True);
				++$Count;
				$Attributes = ArrayValue('Attributes', $Link, array());
				$CssClassSuffix = '';
				if ($Count == 1) $CssClassSuffix = 'First';
				elseif ($Count == $CountItems) $CssClassSuffix = 'Last';
				$Attributes['class'] = trim($CssClassSuffix . 'Crumb ' . ArrayValue('class', $Attributes, ''));
				$Item = '<li'.Attribute($Attributes).'><a'.Attribute($AnchorAttributes).' href="'.$Url.'">'.$Text.'</a>';
				$String .= "\n<ul".Attribute($ListAttributes).'>'.$Item;
			}
		}
		$String .= str_repeat("\n</ul></li>", $Count - 1) . '</ul>';
		return $String;
	}
	
}






