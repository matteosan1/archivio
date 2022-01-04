<?php
class PerPage {
	public $perpage;
    public $pages;
	
	function __construct() {
		$this->perpage = 10;
        $this->pages = 0;
	}
    
	function getAllPageLinks($count) {
		$output = '';
		if (!isset($_GET["page"]))
            $_GET["page"] = 1;
        
        //if($this->perpage != 0)
		$this->pages = ceil($count/$this->perpage);
        
		if($this->pages>1) {
			if($_GET["page"] == 1) 
				$output = $output . '<span class="link first disabled">&#8810;</span><span class="link disabled">&#60;</span>';
			else	
				$output = $output . '<a class="link first" onclick="getresult(\'' . (1) . '\')" >&#8810;</a><a class="link" onclick="getresult(\'' . ($_GET["page"]-1) . '\')" >&#60;</a>';
			
			
			if(($_GET["page"]-3)>0) {
				if($_GET["page"] == 1)
					$output = $output . '<span id=1 class="link current">1</span>';
				else				
					$output = $output . '<a class="link" onclick="getresult(\'' . '1\')" >1</a>';
			}
			if(($_GET["page"]-3)>1) {
                $output = $output . '<span class="dot">...</span>';
			}
			
			for($i=($_GET["page"]-2); $i<=($_GET["page"]+2); $i++)	{
				if($i<1) continue;
				if($i>$this->pages) break;
				if($_GET["page"] == $i)
					$output = $output . '<span id='.$i.' class="link current">'.$i.'</span>';
				else				
					$output = $output . '<a class="link" onclick="getresult(\'' . $i . '\')" >'.$i.'</a>';
			}
			
			if(($this->pages-($_GET["page"]+2))>1) {
				$output = $output . '<span class="dot">...</span>';
			}
			if(($this->pages-($_GET["page"]+2))>0) {
				if($_GET["page"] == $this->pages)
					$output = $output . '<span id=' . ($this->pages) .' class="link current">' . ($this->pages) .'</span>';
				else				
					$output = $output . '<a class="link" onclick="getresult(\'' .  ($this->pages) .'\')" >' . ($this->pages) .'</a>';
			}
			
			if($_GET["page"] < $this->pages)
				$output = $output . '<a  class="link" onclick="getresult(\'' . ($_GET["page"]+1) . '\')" >></a><a  class="link" onclick="getresult(\'' . ($this->pages) . '\')" >&#8811;</a>';
			else				
				$output = $output . '<span class="link disabled">></span><span class="link disabled">&#8811;</span>';
		}
		return $output;
	}
}
?>
