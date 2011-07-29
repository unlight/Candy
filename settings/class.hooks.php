<?php if (!defined('APPLICATION')) exit();

class CandyHooks implements Gdn_IPlugin {
	
	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu =& $Sender->EventArguments['SideMenu'];
		//$Menu->AddLink('Add-ons', 'Candy CMS', 'candy/section/tree', 'Garden.AdminUser.Only');
		$Menu->AddLink('Add-ons', 'Pages', 'candy/page/browse', 'Garden.AdminUser.Only');
		$Menu->AddLink('Add-ons', 'Sections', 'candy/section/tree', 'Garden.AdminUser.Only');
		$Menu->AddLink('Add-ons', 'Chunks', 'candy/chunk/browse', 'Garden.AdminUser.Only');
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
			$RequestArgs = array_map('strtolower', SplitString($Request->RequestUri(), '\/'));
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
						$Data = $SectionModel->GetByURI($Request->RequestUri());
						if ($Data && $Data->RequestUri) $Request->WithURI($Data->RequestUri);
					}
				}
			}
		}
	}
	
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


