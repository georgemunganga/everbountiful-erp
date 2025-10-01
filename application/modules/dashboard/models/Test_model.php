<?php defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author: Bdtask Ltd
    # Author link: https://www.bdtask.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------    

class Test_model extends CI_Model {
 
	private $table = "appsetting";

    
    public function testFuncttt(){
        return 55555555555;
    }

	// public function create($data = [])
	// {	 
	// 	return $this->db->insert($this->table,$data);
	// }
 
	// public function read()
	// {
	// 	return $this->db->select("*")
	// 		->from($this->table)
	// 		->get()
	// 		->row();
	// } 
	
  	// public function update($data = [])
	// {
	// 	return $this->db->where('id',$data['id'])
	// 		->update($this->table,$data); 
	// }

	// public function setData()
	// {
	// 	echo "Test test test test";
	// }
}
