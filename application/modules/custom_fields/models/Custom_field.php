<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * FusionInvoice
 * 
 * A free and open source web based invoicing system
 *
 * @package		FusionInvoice
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012 - 2013 FusionInvoice, LLC
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.com
 * 
 */

class Custom_Field extends CI_Model {

    private $table              = 'custom_fields';              
    private $pk                 = 'custom_field_id';
    private $dOrder             = 'asc';      

    function exists($id, $license_key)
    {
        $this->db->from($this->table);   
        $this->db->where($this->pk, $id);
        $query = $this->db->get();
        
        return ($query->num_rows()==1);
    }

    function count_all($license_key)
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    
    function get_info($id)
    {
        $this->db->from($this->table);   
        $this->db->where($this->pk, $id);
        $query = $this->db->get();
        
        if($query->num_rows()==1)
        {
            return $query->row();
        }
        else
        {

            $obj=new stdClass();

            $fields = $this->db->list_fields($this->table);
            
            foreach ($fields as $field)
            {
                $obj->$field='';
            }
            
            return $obj;
        }
    } 

    function get_by_id($id)
    {
        return $this->db->where($this->pk, $id)->get($this->table)->row();
    }

    function save(&$custom_data, $license_key, $id = NULL)
    {

        $success=false;
        
        //Run these queries as a transaction, we want to make sure we do all or nothing
        $this->db->trans_start();

        $original_record = $this->get_by_id($id);
        // Create the name for the custom field column
        $custom_field_column = strtolower($custom_data['custom_field_table']) . '_custom_' . preg_replace('/[^a-zA-Z0-9_\s]/', '', strtolower(str_replace(' ', '_', $custom_data['custom_field_label'])));

        // Add the custom field column to the db array
        $custom_data['custom_field_column'] = $custom_field_column;

        if (!$id or !$this->exists($id, $license_key))
        {
            
            $success = $this->db->insert($this->table, $custom_data);
            $id = $this->db->insert_id();

            if ($success) {
                // This is a new column - add itusers_custom
                
                if (isset($original_record))
                {
                    if ($original_record->custom_field_column <> $custom_data['custom_field_column'])
                    {
                        // The column name differs from the original - rename it
                        $this->rename_column($custom_data['custom_field_table'].'_custom', $original_record->custom_field_column, $custom_data['custom_field_column']);
                    }
                }
                else
                {
                    $this->add_column($custom_data['custom_field_table'].'_custom', $custom_data['custom_field_column']);
                }
                
            }
            
        }

        $this->db->where($this->pk, $id);
        $success = $this->db->update($this->table, $custom_data);

        $this->db->trans_complete();        
        return $success; 

    }

    private function add_column($table_name, $column_name)
    {
        $this->load->dbforge();

        $column = array(
            $column_name => array(
                'type'       => 'VARCHAR',
                'constraint' => 255
            )
        );

        $this->dbforge->add_column($table_name, $column);
    }

    private function rename_column($table_name, $old_column_name, $new_column_name)
    {
        $this->load->dbforge();
        
        $column = array(
            $old_column_name => array(
                'name'       => $new_column_name,
                'type'       => 'VARCHAR',
                'constraint' => 255
            )
        );

        $this->dbforge->modify_column($table_name, $column);
    }

    public function delete($id)
    {
        $custom_field = $this->get_by_id($id);

        if ($this->db->field_exists($custom_field->custom_field_column, $custom_field->custom_field_table))
        {
            $this->load->dbforge();
            $this->dbforge->drop_column($custom_field->custom_field_table, $custom_field->custom_field_column);
        }

        $this->db->where($this->pk, $id);

        if($this->db->delete($this->table)){
            return true;
        }else{
            return $this->db->error();
        }
    
    }
}

?>