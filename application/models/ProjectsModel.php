<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProjectsModel extends CI_Model
{
    function getAllProjects()
    {
        return $this->db->get('seprojects')->result();
    }

    function getRunningProjects()
    {
        return $this->db->from('seprojects')->where('seproj_status', 'running')->get()->result();
    }

    function getCompletedProjects()
    {
        return $this->db->from('seprojects')->where('seproj_status', 'completed')->get()->result();
    }

    function getPendingProjects()
    {
        return $this->db->from('seprojects')->where('seproj_status', 'pending')->get()->result();
    }

    function count_all_projects()
    {
        return $this->db->count_all('seprojects');
    }

    function count_running_projects()
    {
        return $this->db->where('seproj_status', 'running')->count_all_results('seprojects');
    }

    function count_pending_projects()
    {
        return $this->db->where('seproj_status', 'pending')->count_all_results('seprojects');
    }

    function count_completed_projects()
    {
        return $this->db->where('seproj_status', 'completed')->count_all_results('seprojects');
    }

    function getProjectById($id)
    {
        return $this->db->where('seproj_id', $id)->get('seprojects')->row();
    }

    function getProjectsByStatus($status)
    {
        return $this->db->where('seproj_status', $status)->get('seprojects')->result();
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

    function delete_project($id)
    {
        $this->db->where('seproj_id', $id);
        return $this->db->delete('seprojects');
    }

    /**
     * Check whether a project with the given name already exists.
     * Comparison is case-insensitive (relies on MySQL default collation).
     *
     * @param  string   $name      Project name to check.
     * @param  int|null $excludeId Optionally exclude a project ID (useful for edit checks).
     * @return object|null         Full project row or null.
     */
    function getProjectByName($name, $excludeId = null)
    {
        $this->db->where('seproj_name', trim($name));

        if ($excludeId) {
            $this->db->where('seproj_id !=', (int) $excludeId);
        }

        return $this->db->get('seprojects')->row();
    }

    public function getRecentProjects($limit = 5)
    {
        $this->db->order_by('seproj_id', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('seprojects')->result();
    }

    public function getUpcomingDeadlines($limit = 3)
    {
        $this->db->where('seproj_status !=', 'completed');
        $this->db->where('seproj_deadline >=', date('Y-m-d'));
        $this->db->order_by('seproj_deadline', 'ASC');
        $this->db->limit($limit);
        return $this->db->get('seprojects')->result();
    }
}
