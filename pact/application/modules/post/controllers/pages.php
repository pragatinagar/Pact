<?php
	class Pages extends CI_Controller{
		
		function __construct(){
			parent::__construct();					
		}
		
		function index()
		{	
			$this->showpage(1);
		}
		
		function showpage($slug) {
			echo'a';
		}
}
?>
