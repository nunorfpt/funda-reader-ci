<?php
class Request extends CI_Model {

    private $table = 'funda_requests';
    
    private $id;
    private $url_requested;
    private $time_requested;

    public function __construct()
    {
        parent::__construct();
        $this->load->driver('cache');
    }

    public function insert($array)
    {
        $ret = $this->db->insert($this->table, $array);
        return $ret ? $this->db->insert_id() : 0;
    }
}
?>