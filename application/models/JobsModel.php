<?php
// Suropriyo Eterprise
// Howrah

defined('BASEPATH') OR exit('No direct script access allowed');

class JobsModel extends CI_Model
{
    public function get_all_jobs() {
        return $this->db->get('sejobs')->result();
    }

    function get_avilable_jobs_query()
    {
        return $this->db->get_compiled_select("sejobs");
    }

    function get_search_in_anyfield_query($mq = '')
    {
        $this->db->from('sejobs');
        $this->db->or_like('sejob_jobtitle', $mq);
        $this->db->or_like('sejob_experience', $mq);
        $this->db->or_like('sejob_address', $mq);
        $this->db->or_like('sejob_workinghours', $mq);
        $this->db->or_like('sejob_skills', $mq);
        $this->db->or_like('sejob_salary', $mq);
        $this->db->or_like('sejob_desc', $mq);
        $this->db->or_like('sejob_state', $mq);
        $this->db->or_like('sejob_urgency', $mq);
        return $this->db->get_compiled_select();
    }

    function filter_jobs_query($title = '', $location = '', $skills = '', $experience = '')
    {
        $dynamic_query = array();
        if (trim($title) != '') {
            $dynamic_query['sejob_jobtitle'] = $title;
        }
        if (trim($location) != '') {
            $dynamic_query['sejob_address'] = $location;
        }
        if (trim($skills) != '') {
            $dynamic_query['sejob_skills'] = $skills;
        }

        if (count($dynamic_query) <= 0 && empty($experience)) {
            return $this->db->get_compiled_select("sejobs");
        } else {
            $this->db->from('sejobs');
            
            foreach ($dynamic_query as $key => $value) {
                $this->db->like($key, $value);
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
                        $this->db->where("sejob_experience >", 7); // FIXED: Passing Integer instead of '7plus'
                        break;
                    default:
                        $this->db->where("sejob_experience", (int)$experience);
                        break;
                }
            }

            return $this->db->get_compiled_select();
        }
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
        return $q . " ORDER BY sejob_salary ";
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
    public function get_job_by_id($job_id)
    {
        $this->db->where('sejob_id', $job_id);
        return $this->db->get('sejobs')->row();
    }
}
?>