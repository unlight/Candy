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
		$this->_ChunksEdit($Sender);
		$this->_AddCreatePageModule($Sender);
		$this->_AddTopMenu($Sender);
	}

	protected function _ChunksEdit($Sender) {
		if (Gdn::Session()->CheckPermission('Candy.Chunks.Edit')) {
			$Sender->AddJsFile('jquery.inline-edit.js', 'candy');
			$Sender->AddJsFile('candy.js', 'candy');
			$Sender->AddCssFile('candy.css', 'candy');
		}
	}

	protected function _AddCreatePageModule($Sender) {
		if (Gdn::Session()->CheckPermission('Candy.Pages.Add')) {
			$Router = Gdn::Router();
			$Default404 = GetValueR('Routes.Default404.Destination', $Router);
			if ($Default404 == $Sender->SelfUrl) {
				$Sender->AddModule(new CreatePageModule($Sender, 'candy'));
			}
		}
	}

	protected function _AddTopMenu($Sender) {
		$Menu = $Sender->Menu;
		$Version = C('Candy.Version');
		if ($Menu && $Version >= 0.36) {
			$SectionModel = Gdn::Factory('SectionModel');
			$Items = $SectionModel->GetNodes(array('InTopMenu' => 1, 'CacheNodes' => True));
			foreach ($Items as $Item) {
				$ParentItem = $SectionModel->GetNode($Item->ParentID, array('InTopMenu' => 1));
				$Url = SectionUrl($Item);
				if ($ParentItem == FALSE) {
					$Menu->AddLink($Item->Name, $Item->Name, $Url, FALSE);
				} else {
					$Menu->AddLink($ParentItem->Name, $Item->Name, $Url, FALSE);
				}
			}
		}
	}

	public function Gdn_Dispatcher_BeforeDispatch_Handler($Sender) {
		$Request = Gdn::Request();
		$RequestUri = $Request->RequestUri();
		if (Gdn::Router()->GetRoute($RequestUri) === False) {
			$RequestArgs = SplitUpString($RequestUri, '/', 'strtolower');
			if (array_key_exists(0, $RequestArgs)) {
				$ApplicationFolders = $Sender->EnabledApplicationFolders();
				$bFoundApplication = in_array($RequestArgs[0], $ApplicationFolders);
				if ($bFoundApplication === False) {
					$PathParts = array('controllers', 'class.'.$RequestArgs[0].'controller.php');
					$ControllerFileName = CombinePaths($PathParts);
					$ControllerPath = Gdn_FileSystem::FindByMapping('controller', PATH_APPLICATIONS, $ApplicationFolders, $ControllerFileName);
					if (!$ControllerPath || !file_exists($ControllerPath)) {
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
		include PATH_APPLICATIONS . '/candy/settings/structure.php';
	}
	
	public function OnDisable() {
	}

	
}