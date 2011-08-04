<?php if (!defined('APPLICATION')) exit();

class CandyHooks implements Gdn_IPlugin {
	
	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu =& $Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Add-ons', T('Pages'), 'candy/page/browse', 'Candy.Settings.View');
		$Menu->AddLink('Add-ons', T('Sections'), 'candy/section/tree', 'Candy.Settings.View');
		$Menu->AddLink('Add-ons', T('Chunks'), 'candy/chunk/browse', 'Candy.Settings.View');
	}
	
	public function Base_Render_Before($Sender) {
		if ($Sender->Application == 'Candy' && $Sender->DeliveryType() == DELIVERY_TYPE_ALL) {
			$this->BreadCrumbsAssetRender($Sender);
		}
	}

	protected function BreadCrumbsAssetRender($Sender) {
		$BreadCrumbsModule =& $Sender->Assets['BreadCrumbs']['BreadCrumbsModule'];
		if ($BreadCrumbsModule) {
			$AssetTarget = C('Candy.Modules.BreadCrumbsAssetTarget');
			if ($AssetTarget) {
				$Sender->AddModule($BreadCrumbsModule, $AssetTarget);
				unset($Sender->Assets['BreadCrumbs']);
			}
		}
	}
	
	public function Gdn_Dispatcher_BeforeDispatch_Handler($Sender) {
		if (!C('Candy.Version')) return;
		$Request = Gdn::Request();
		$Route = Gdn::Router()->GetRoute($Request->RequestUri());
		if ($Route === False) {
			$RequestArgs = SplitUpString($Request->RequestUri(), '/', 'strtolower');
			if (array_key_exists(0, $RequestArgs)) {
				$ApplicationFolders = $Sender->EnabledApplicationFolders();
				$bFoundApplication = in_array($RequestArgs[0], $ApplicationFolders);
				if ($bFoundApplication === False) {
					$PathParts = array('controllers');
					$PathParts[] = 'class.'.$RequestArgs[0].'controller.php';
					$ControllerFileName = CombinePaths($PathParts);
					$ControllerPath = Gdn_FileSystem::FindByMapping('controller', PATH_APPLICATIONS, $ApplicationFolders, $ControllerFileName);
					if (!$ControllerPath || !file_exists($ControllerPath)) {
						$SectionModel = Gdn::Factory('SectionModel');
						$RequestUri = $Request->RequestUri();
						$Sender->EventArguments['RequestUri'] =& $RequestUri;
						$Sender->FireEvent('BeforeGetSection');
						$Data = $SectionModel->GetByURI($RequestUri);
						if ($Data && $Data->RequestUri) $Request->WithURI($Data->RequestUri);
					}
				}
			}
		}
	}
	
/*	public function ContentController_ContentPage_Handler($Sender) {
		if (!($Sender->DeliveryType() == DELIVERY_TYPE_ALL && $Sender->SyndicationMethod == SYNDICATION_NONE)) return;
		$Head =& $Sender->Head;
		$Content =& $Sender->EventArguments[''];
		if ($Head) {
			$Head->AddTag('meta', array('name' => 'robots', 'content' => 'noindex', '_sort' => 0));
		}
	}*/
	
	public function Setup() {
		include(PATH_APPLICATIONS . '/candy/settings/structure.php');
		$ApplicationInfo = array();
		include(CombinePaths(array(PATH_APPLICATIONS . '/candy/settings/about.php')));
		$Version = GetValue('Version', GetValue('Candy', $ApplicationInfo));
		SaveToConfig('Candy.Version', $Version);
	}
	
	public function OnDisable() {
		RemoveFromConfig('Candy.Version');
	}
	
	// TEST
/*	public function PluginController_CandyChunkTest_Create($Sender) {
		$Sender->AddJsFile('applications/candy/js/jquery.inline-edit.js');
		$Sender->AddJsFile('applications/candy/js/candy.js');
		$Sender->AddCssFile('applications/candy/design/candy.css');
		$Sender->View = $Sender->FetchViewLocation('chunk', 'test', 'candy');
		$Sender->Render();
	}*/
	

	
}


