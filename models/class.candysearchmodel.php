<?php if (!defined('APPLICATION')) exit();

class CandySearchModel extends Gdn_Model {

	public function SectionSql($SearchModel) {
		// Build search part of query
		$SearchModel->AddMatchSql($this->SQL, 's.Name', '10');
		// Build base query
		$this->SQL
			->Select('s.SectionID as PrimaryID')
			->Select('s.Name as Title')
			->Select('Null as Summary')
			->Select('"Text" as Format')
			->Select('Null as CategoryID')
			->Select('s.Url, s.URI, s.RequestUri', 'coalesce', 'Url')
			->Select('Now() as DateInserted')
			->Select('0 as UserID')
			//->Select('Null as Name')
			//->Select('Null as Photo')
			->From('Section s');
		// Get and unset SQL
		$Result = $this->SQL->GetSelect();
		$this->SQL->Reset();
		return $Result;
	}
	
	public function ChunkSql($SearchModel) {
		// Build search part of query
		$SearchModel->AddMatchSql($this->SQL, 'Name, Body', '50');
		// Build base query
		$this->SQL
			->Select('ChunkID as PrimaryID')
			->Select('Name as Title')
			->Select('Body as Summary')
			->Select('"xHtml" as Format')
			->Select('Null as CategoryID')
			->Select('Url')
			->Select('DateInserted')
			->Select('0 as UserID')
			//->Select('Null as Name')
			//->Select('Null as Photo')
			->From('Chunk');
		// Get and unset SQL
		$Result = $this->SQL->GetSelect();
		$this->SQL->Reset();
		return $Result;
	}
	
	
	public function PageSql($SearchModel) {
		// Build search part of query
		$SearchModel->AddMatchSql($this->SQL, 'Title, Body', '20');
		// Build base query
		$this->SQL
			->Select('PageID as PrimaryID')
			->Select('Title')
			->Select('Body as Summary')
			->Select('Format')
			->Select('Null as CategoryID')
			->Select('PageID', "concat('/content/page/', %s)", 'Url')
			->Select('DateInserted')
			->Select('0 as UserID')
			//->Select('Null as Name')
			//->Select('Null as Photo')
			->Where('Visible', 1, False, False)
			->From('Page');
		// Get and unset SQL
		$Result = $this->SQL->GetSelect();
		$this->SQL->Reset();
		return $Result;
	}
	
	public function Search($SearchModel) {
		$SearchModel->AddSearch($this->SectionSql($SearchModel));
		$SearchModel->AddSearch($this->PageSql($SearchModel));
		$SearchModel->AddSearch($this->ChunkSql($SearchModel));
	}
}




