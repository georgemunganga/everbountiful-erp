<?php 

defined('BASEPATH') OR exit('No direct script access allowed');
 #------------------------------------    
    # Author: Bdtask Ltd
    # Author link: https://www.bdtask.com/
    # Dynamic style php file
    # Developed by :Isahaq
    #------------------------------------   


class AI_Setting_model extends CI_Model 
{
    private $table = "ai_setting";
    public function create($data = [])
    {	 
        return $this->db->insert($this->table,$data);
    }
    
    public function update($data = [])
	{
		return $this->db->where('setting_id',$data['setting_id'])
			->update($this->table,$data); 
	} 


    public function read()
    {
         $data = $this->db->select("*")
            ->from($this->table)
            ->get()
            ->row();
            return $data;
    } 

    
    

}






?>


