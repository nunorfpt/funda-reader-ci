<?php
class Makelaar extends CI_Model {

    private $table = 'funda_makelaars';
    
    private $id;
    private $makelaarId;
    private $makelaarNaam;

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

    public function emptyTable()
    {
        $this->db->empty_table($this->table);
    }

    public function getTop($howmany)
    {
        $sql = "SELECT MakelaarNaam, COUNT(id) as MarkelaarCount FROM funda_makelaars GROUP BY MakelaarId ORDER BY MarkelaarCount DESC LIMIT ".$howmany;
        $query = $this->db->query($sql);
        return $query->result();
    }

}
?>