<?php
	class Admin extends CI_Controller{
		
		function __construct(){
			parent::__construct();
			$this->load->model('pages_model');
		}
		
		function index()
		{
			$where = '';
			$and = '';
			$data['pagetype']='';
			if($this->input->get('pagetype'))
			{				
				$data['pagetype']=$this->input->get('pagetype');
				$where.= ' pc_name = "'.$this->input->get('pagetype').'"';
				$and = ' and ';
			}else{
				$where.= ' pc_name = "General"';
				$and = ' and ';
			}
			if($this->input->get('searchpage'))
			{
				$where.= $and.' page_title like "%'.$this->input->get('searchpage').'%"';
				$and = ' and ';
			}			
			$data['page_title'] = 'Pages';
			$data['total_pages'] = $this->pages_model->totalPages($where);
			//echo $query=$this->db->last_query(); exit;
			$data['pages'] = $this->pages_model->get_pages($this->settings->item('rows_per_page'), @$_GET['page'], $where);			
			create_pagination(base_url().'admin/pages/index/?pagi=1',$data['total_pages'], 4, $this->settings->item('rows_per_page'));																		
			$this->template->load('template','admin/page_list',$data);
			//$this->load->view('listing_pages',$data);
		}
						
		function add()
		{
			if($this->input->post('submitDetail'))
			{			
				$this->add_edit();
				die();
			}
			$page = $this->input->get('page');
			if(isset($page))
			$data['pagetype'] =  $page;
			$data['page_title'] = 'Add Page';
			$this->template->load('template','admin/page_form', @$data);
		}
	
		function edit($id)
		{
			if($this->input->post('submitDetail'))
			{
				$this->add_edit($id);
				die();
			}
			$data['details'] = $this->general_db_model->getById('static_pages', 'id', $id);	
			//print_r($data['details']);
			$data['page_title'] = 'Edit Page';
			$this->template->load('template','admin/page_form', @$data);
		}
	
		function add_edit($id=NULL)
		{
			if($id)
			{
				$not = array('submitDetail','fileList','page_slug','pack');			
				$post = $this->mylibrary->get_post_array($not);
				$home_image = $this->input->post('fileList');
				$post['home_image'] = $home_image;
				$this->general_db_model->update('static_pages', $post, 'id = '.$id);
				$this->session->set_flashdata('display_message', 'Page successfully updated.');
			}
			else
			{
				$not = array('submitDetail','fileList','pack');			
				$post = $this->mylibrary->get_post_array($not);
				$home_image = $this->input->post('fileList');
				$post['home_image'] = $home_image;
				$this->general_db_model->insert('static_pages', $post);
				$this->session->set_flashdata('display_message', 'Page successfully added.');
			}		
			$goto ='admin/'.$this->input->post('pack');
			redirect($goto);
		}
	
		function delete($id)
		{
			$this->general_db_model->del_img($id,'static_pages','banners');
			$this->general_db_model->delete('static_pages', 'id = '.$id);			
			$this->session->set_flashdata('display_message', 'Page successfully deleted.');
			redirect($_SERVER['HTTP_REFERER']);
		}
		
		function delete_image()
		{
			$img = $this->input->post('imgName');
			$imgpath = $this->config->item('uploads').'banners/'.$img;//echo $imgpath; exit;
			if(file_exists($imgpath)){
				unlink($imgpath);
				echo 'Image successfully deleted!';
			}
			else
			{
				echo 'File does not exist!';
			}
		}
		
		function update_status()
		{ 
			$id = $this->input->get('id');
			$status = $this->input->get('status');
			//echo $status; die();
			if($status == 'Publish')
				$data['status'] = 'Unpublish';
			else
				$data['status'] = 'Publish';
			$this->general_db_model->update('static_pages', $data, 'id = '.$id);
		   
			echo $data['status'];
		}
		
}
?>