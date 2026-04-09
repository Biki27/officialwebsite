<?php
// Suropriyo Eterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');

class JobsModel extends CI_Model
{
    public function get_all_jobs()
    {
        // Exclude deleted jobs from the HR view
        $this->db->where('sejob_state !=', 'deleted');
        return $this->db->get('sejobs')->result();
    }

    function get_avilable_jobs_query()
    {
        return $this->db->get_compiled_select("sejobs");
    }

    function get_search_in_anyfield_query($mq = '')
    {
        $this->db->from('sejobs');
        $this->db->where('sejob_state !=', 'deleted');

        if (!empty(trim($mq))) {
            $this->db->group_start();
            $this->db->like('sejob_jobtitle', $mq);
            $this->db->or_like('sejob_experience', $mq);
            $this->db->or_like('sejob_address', $mq);
            $this->db->or_like('sejob_skills', $mq);
            $this->db->or_like('sejob_desc', $mq);
            $this->db->group_end();
        }

        // THIS FIXES THE INITIAL SEARCH AND KEYWORD SEARCH ORDER
        $this->db->order_by("CASE WHEN sejob_state = 'active' THEN 1 ELSE 2 END", "ASC");
        $this->db->order_by('sejob_dateofpost', 'DESC');

        return $this->db->get_compiled_select();
    }

    function filter_jobs_query($title = '', $location = '', $skills = '', $experience = '')
    {
        // Select everything, plus a concatenated string of skills
        $this->db->select('sejobs.*, GROUP_CONCAT(seskills.skill_name SEPARATOR ", ") as sejob_skills');
        $this->db->from('sejobs');

        // Join the mapping and skills tables
        $this->db->join('sejob_skills_map', 'sejob_skills_map.job_id = sejobs.sejob_id', 'left');
        $this->db->join('seskills', 'seskills.skill_id = sejob_skills_map.skill_id', 'left');

        $this->db->where('sejob_state !=', 'deleted');

        if (trim($title) != '') {
            $this->db->like('sejob_jobtitle', $title);
        }
        if (trim($location) != '') {
            $this->db->like('sejob_address', $location);
        }

        // --- THE FIX: Subquery for exact skill matching ---
        if (trim($skills) != '') {
            // Escape the skill string to prevent SQL injection
            $escaped_skill = $this->db->escape($skills);

            // Look inside the mapping table to find matching jobs, 
            // without stripping the other skills from the main query's GROUP_CONCAT
            $this->db->where("sejobs.sejob_id IN (
                SELECT map.job_id 
                FROM sejob_skills_map map
                JOIN seskills s ON s.skill_id = map.skill_id
                WHERE s.skill_name = $escaped_skill
            )", NULL, FALSE);
        }


        if (trim($experience) != '') {
            switch ($experience) {
                case '1':
                    $this->db->where("sejob_experience <=", 1);
                    break;
                case '3':
                    $this->db->where("sejob_experience <=", 3);
                    break;
                case '7':
                    $this->db->where("sejob_experience <=", 7);
                    break;
                case '7plus':
                    $this->db->where("sejob_experience >", 7);
                    break;
                default:
                    $this->db->where("sejob_experience", (int) $experience);
                    break;
            }
        }

        $this->db->group_by('sejobs.sejob_id');
        /**
         * NEW SORTING LOGIC:
         * We want 'active' jobs first, then 'inactive'.
         * Since 'active' comes before 'inactive' alphabetically, a simple ASC sort works,
         * but a CASE statement is more robust if you add more states later.
         */
        $this->db->order_by("CASE WHEN sejob_state = 'active' THEN 1 ELSE 2 END", "ASC");

        // Secondary sort: Show the newest jobs first within those groups
        $this->db->order_by('sejob_dateofpost', 'DESC');

        return $this->db->get_compiled_select();
    }

    function get_jobmodel_query_result($query = '')
    {
        $mquery = $this->db->query($query);
        return $mquery->result();
    }

    function get_jobs_orderby_date($title = '', $location = '', $skills = '', $experience = '')
    {
        $query = $this->filter_jobs_query($title, $location, $skills, $experience);
        return $query . ' ORDER BY sejob_dateofpost ';
    }

    function get_jobs_orderby_salary($title = '', $location = '', $skills = '', $experience = '')
    {
        $q = $this->filter_jobs_query($title, $location, $skills, $experience);
        // This assumes filter_jobs_query doesn't already have an ORDER BY, 
        // or you can modify these to append to the existing query.
        return $q . " ORDER BY CASE WHEN sejob_state = 'active' THEN 1 ELSE 2 END ASC, sejob_salary DESC";
    }

    function get_jobs_orderby_experience($title = '', $location = '', $skills = '', $experience = '')
    {
        $q = $this->filter_jobs_query($title, $location, $skills, $experience);
        return $q . " ORDER BY sejob_experience ";
    }

    function limit_query($mquery = '', $limit = '', $offset = '')
    {
        $query = $mquery;
        if (trim($limit) != '') {
            $query .= " LIMIT " . $limit;
            if (trim($offset) != '') {
                $query .= " OFFSET " . $offset;
            }
        }
        return $query;
    }
    // Fetches a single job to display on the apply page
    // public function get_job_by_id($job_id)
    // {
    //     $this->db->where('sejob_id', $job_id);
    //     return $this->db->get('sejobs')->row();
    // }
    public function get_job_by_id($job_id)
    {
        $this->db->where('sejob_id', $job_id);
        $this->db->where('sejob_state !=', 'deleted'); // Hide if deleted
        return $this->db->get('sejobs')->row();
    }


    public function save_job_skills($job_id, $skills_array)
    {
        // 1. Clear old skills for this job (useful for updates)
        $this->db->where('job_id', $job_id);
        $this->db->delete('sejob_skills_map');

        if (empty($skills_array))
            return;

        // 2. Loop through submitted skills
        foreach ($skills_array as $skill_name) {
            $skill_name = trim($skill_name);
            if (empty($skill_name))
                continue;

            // 3. Check if skill exists in the master table
            $this->db->where('skill_name', $skill_name);
            $query = $this->db->get('seskills');

            if ($query->num_rows() > 0) {
                $skill_id = $query->row()->skill_id;
            } else {
                // If it's a new skill, insert it first
                $this->db->insert('seskills', ['skill_name' => $skill_name]);
                $skill_id = $this->db->insert_id();
            }

            // 4. Map the skill to the job
            $this->db->insert('sejob_skills_map', [
                'job_id' => $job_id,
                'skill_id' => $skill_id
            ]);
        }
    }
    // Fetch all unique skills for the dropdown filter
    public function get_all_skills()
    {
        $this->db->order_by('skill_name', 'ASC');
        return $this->db->get('seskills')->result();
    }
}
?>