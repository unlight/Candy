<?php if (!defined('APPLICATION')) exit();

class CandyHooks implements Gdn_IPlugin {
	
	public function SearchModel_Search_Handler($Sender) {
		$SearchModel = new CandySearchModel();
		$SearchModel->Search($Sender);
	}
	
	public static function AddModules($Controller, $Section) {
		$SectionModel = Gdn::Factory('SectionModel');
		$Controller->SectionPath = $SectionModel->GetPath($Section);
		// Side menu.
		if (C('Candy.Modules.ShowAjarSideMenu')) {
			$Controller->SectionsModule = new SectionsModule($Controller, 'candy');
			$Controller->SectionsModule->SetAjarData($Controller->SectionPath);
			$Controller->AddModule($Controller->SectionsModule);
		}
		// Breadcrumbs.
		$Controller->BreadCrumbsModule = new BreadCrumbsModule($Controller, 'candy');
		$Controller->BreadCrumbsModule->SetLinks($Controller->SectionPath);
		$Controller->AddModule($Controller->BreadCrumbsModule);
		
		if (empty($Controller->SectionID)) $Controller->SectionID = GetValue('SectionID', $Section, $Section);
		
		$Controller->EventArguments['Section'] = $Section;
		$Controller->FireEvent('CandyModules');

	}
		
	public function Base_GetAppSettingsMenuItems_Handler($Sender) {
		$Menu =& $Sender->EventArguments['SideMenu'];
		$Menu->AddLink('Add-ons', T('Pages'), 'candy/page/browse', 'Candy.Settings.View');
		$Menu->AddLink('Add-ons', T('Sections'), 'candy/section/tree', 'Candy.Settings.View');
		$Menu->AddLink('Add-ons', T('Chunks'), 'candy/chunk/browse', 'Candy.Settings.View');
	}
	
	public function Base_Render_Before($Sender) {
		$DeliveryTypeAll = ($Sender->DeliveryType() == DELIVERY_TYPE_ALL);
		if ($Sender->Application == 'Candy' && $DeliveryTypeAll) $this->BreadCrumbsAssetRender($Sender);
		if ($DeliveryTypeAll) {
			$Default404 = GetValueR('Routes.Default404', $Sender);
			if (is_array($Default404)) {
				if (in_array($Sender->SelfUrl, $Default404) && CheckPermission('Candy.Pages.Add')) {
					$Sender->AddModule(new CreatePageModule($Sender, 'candy'));
				}
			}
			if (Gdn::Session()->CheckPermission('Candy.Chunks.Edit')) {
				$Sender->AddJsFile('jquery.inline-edit.js', 'candy');
				$Sender->AddJsFile('candy.js', 'candy');
				$Sender->AddCssFile('candy.css', 'candy');
			}
		}
	}

	protected function BreadCrumbsAssetRender($Sender) {
		if (isset($Sender->Assets['BreadCrumbs']['BreadCrumbsModule'])) {
			$BreadCrumbsModule =& $Sender->Assets['BreadCrumbs']['BreadCrumbsModule'];
			if ($BreadCrumbsModule) {
				$AssetTarget = C('Candy.Modules.BreadCrumbsAssetTarget');
				if ($AssetTarget) {
					//$BreadCrumbsModule->bCustomAssetTarget = True;
					$Sender->AddModule($BreadCrumbsModule, $AssetTarget);
					unset($Sender->Assets['BreadCrumbs']);
				}
			}
		}
	}
	
	public function Gdn_Dispatcher_BeforeDispatch_Handler($Sender) {
		$Request = Gdn::Request();
		$RequestUri = $Request->RequestUri();
		$Route = Gdn::Router()->GetRoute($RequestUri);
		if ($Route === False) {
			$RequestArgs = SplitUpString($RequestUri, '/', 'strtolower');
			if (array_key_exists(0, $RequestArgs)) {
				$ApplicationFolders = $Sender->EnabledApplicationFolders();
				$bFoundApplication = in_array($RequestArgs[0], $ApplicationFolders);
				if ($bFoundApplication === False) {
					$PathParts = array('controllers', 'class.'.$RequestArgs[0].'controller.php');
					$ControllerFileName = CombinePaths($PathParts);
					$ControllerPath = Gdn_FileSystem::FindByMapping('controller', PATH_APPLICATIONS, $ApplicationFolders, $ControllerFileName);
					if (!$ControllerPath || !file_exists($ControllerPath)) {
						//$RequestUri = trim($Request->RequestUri(), '/');
						$Sender->EventArguments['RequestUri'] =& $RequestUri;
						$Sender->FireEvent('BeforeGetRoute');
						$NewRequest = CandyModel::GetRouteRequestUri($RequestUri);
						if ($NewRequest) $Request->WithURI($NewRequest);
					}
				}
			}
		}
	}
	
	public function Setup() {
		include(PATH_APPLICATIONS . '/candy/settings/structure.php');
	}
	
	public function OnDisable() {
	}

	
}


