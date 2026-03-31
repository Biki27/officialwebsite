<?php
// This code is Written by :
// PAPPU BISWAS
// Suropriyo Enterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectsModel extends CI_Model
{
    function getAllProjects()
    {
        return $this->db->get('seprojects')->result();
    }

    function getRunningProjects()
    {
        $res = $this->db->from('seprojects')->where('seproj_status', 'running')->get()->result();
        return $res;
    }

    function getCompletedProjects()
    {
        $res = $this->db->from('seprojects')->where('seproj_status', 'completed')->get()->result();
        return $res;
    }

    function getPendingProjects()
    {
        $res = $this->db->from('seprojects')->where('seproj_status', 'pending')->get()->result();
        return $res;
    }

    function count_all_projects()
    {
        return $this->db->count_all('seprojects');
    }

    function count_running_projects()
    {
        return $this
            ->db
            ->where('seproj_status', 'running')
            ->count_all_results('seprojects');
    }

    function count_pending_projects()
    {
        return $this
            ->db
            ->where('seproj_status', 'pending')
            ->count_all_results('seprojects');
    }

    function count_completed_projects()
    {
        return $this
            ->db
            ->where('seproj_status', 'completed')
            ->count_all_results('seprojects');
    }

    function getProjectById($id)
    {
        return $this
            ->db
            ->where('seproj_id', $id)
            ->get('seprojects')
            ->row();
    }
    function getProjectsByStatus($status)
    {
        return $this->db->where('seproj_status', $status)
            ->get('seprojects')
            ->result();
    }

    function insert_project($data)
    {
        return $this->db->insert('seprojects', $data);
    }

    function update_project($id, $data)
    {
        $this->db->where('seproj_id', $id);
        return $this->db->update('seprojects', $data);
    }
}
?>