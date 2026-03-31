<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProductsModel extends CI_Model
{
    public function insert_product($data)
    {
        return $this->db->insert('seproducts', $data);
    }
    public function get_all_products()
    {
        return $this->db->get('seproducts')->result();
    }
    public function get_product($id)
    {
        return $this->db->get_where('seproducts', ['seprod_id' => $id])->row();
    }

    public function update_product($id, $data)
    {
        $this->db->where('seprod_id', $id);
        return $this->db->update('seproducts', $data);
    }

    public function delete_product($id)
    {
        $this->db->where('seprod_id', $id);
        return $this->db->delete('seproducts');
    }

}

?>