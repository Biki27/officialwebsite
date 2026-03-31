<?php
defined('BASEPATH') or exit('No direct script access allowed');

class JobApplicationModel extends CI_Model
{
    // Fixed: Now joins with secandidates to get the name and email
    public function get_applicant_by_id($id)
    {
        $this->db->select('sejobapplicant.*, secandidates.full_name AS sejoba_name, secandidates.email AS sejoba_email');
        $this->db->from('sejobapplicant');
        $this->db->join('secandidates', 'sejobapplicant.candidate_id = secandidates.id', 'left');
        $this->db->where('sejobapplicant.sejoba_id', $id);
        $query = $this->db->get();
        return $query->row();
    }


    public function register_applicant($apdata, $resume_path = '')
    {
        if (trim($resume_path) == '') {
            return 1;
        }

        $applicant_info = array(
            'candidate_id' => $apdata['candidate_id'],
            'job_id' => $apdata['job_id'],
            'sejoba_phone' => $apdata['phone'],
            'sejoba_position' => $apdata['position'] ?? '',
            'sejoba_resume' => $resume_path,
            'sejoba_experience' => $apdata['experience'],
            'sejoba_exp_salary' => $apdata['salary'],
            'sejoba_coverletter' => $apdata['coverletter'],
            'sejoba_state' => 'applied',
            'sejoba_atime' => date('Y-m-d H:i:s')
        );

        $this->db->trans_start();
        $this->db->insert('sejobapplicant', $applicant_info);
        $new_applicant_id = $this->db->insert_id(); // Get the ID of the new application
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            // --- DATA FETCHING FOR EMAIL ---

            $this->db->select('
            secandidates.email, 
            secandidates.full_name, 
            sejobs.sejob_jobtitle, 
            sejobapplicant.sejoba_phone
        ');
            $this->db->from('sejobapplicant');
            $this->db->join('secandidates', 'sejobapplicant.candidate_id = secandidates.id', 'left');
            $this->db->join('sejobs', 'sejobapplicant.job_id = sejobs.sejob_id', 'left');
            $this->db->where('sejobapplicant.sejoba_id', $new_applicant_id);

            $details = $this->db->get()->row();

            if ($details) {
                $this->load->model('EmailModel');
                // Passing the fetched data to your existing function
                $this->EmailModel->send_applicant_submission_email(
                    $details->email,           // From secandidates
                    $details->full_name,       // From secandidates
                    $details->sejob_jobtitle,  // From sejobs
                    $details->sejoba_phone     // From sejobapplicant
                );
            }

            return 0;
        }

        return 1;
    }

    // Fixed: Added JOIN to ensure you get candidate details
    public function get_applicant_info($sejobaid = '')
    {
        if (trim($sejobaid) == '') {
            return [];
        } else {
            $this->db->select('sejobapplicant.*, secandidates.full_name AS sejoba_name, secandidates.email AS sejoba_email');
            $this->db->from('sejobapplicant');
            $this->db->join('secandidates', 'sejobapplicant.candidate_id = secandidates.id', 'left');
            $this->db->where('sejoba_id', $sejobaid);
            $this->db->limit(1);
            return $this->db->get()->result();
        }
    }

    public function get_all_applicants()
    {
        $this->db->select('
            sejobapplicant.*, 
            secandidates.full_name AS sejoba_name, 
            secandidates.email AS sejoba_email, 
            sejobs.sejob_jobtitle AS sejoba_position
        ');
        $this->db->from('sejobapplicant');
        $this->db->join('secandidates', 'sejobapplicant.candidate_id = secandidates.id', 'left');
        $this->db->join('sejobs', 'sejobapplicant.job_id = sejobs.sejob_id', 'left');
        $this->db->order_by('sejobapplicant.sejoba_atime', 'DESC');

        return $this->db->get()->result();
    }

    public function update_applicant_review($id, $status, $comment)
    {
        $data = array(
            'sejoba_state' => $status,
            'sejoba_comment' => $comment
        );

        $this->db->where('sejoba_id', $id);
        return $this->db->update('sejobapplicant', $data);
    }

    public function schedule_interview($applicant_id = '', $interview_data = array())
    {
        if (empty($applicant_id) || empty($interview_data)) {
            return ['code' => 1, 'message' => 'Invalid parameters'];
        }

        $this->db->trans_start();

        $update_data = array(
            'sejoba_state' => $interview_data['round_status'],
            'sejoba_interview_date' => $interview_data['date'] ?? null,
            'sejoba_interview_time' => $interview_data['time'] ?? null,
            'sejoba_interview_location' => $interview_data['location'] ?? null,
            'sejoba_interview_scheduled_by' => $interview_data['scheduled_by'] ?? null,
            'sejoba_interview_scheduled_at' => date('Y-m-d H:i:s')
        );

        $this->db->where('sejoba_id', $applicant_id);
        $this->db->update('sejobapplicant', $update_data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['code' => 1, 'message' => 'Failed to schedule interview'];
        } else {
            return ['code' => 0, 'message' => 'Interview scheduled successfully'];
        }
    }

    // Fixed: status updated to 'interviewing' to match the Enum in your SQL
    public function get_interview_scheduled_applicants()
    {
        $this->db->select('sejobapplicant.*, secandidates.full_name AS sejoba_name, sejobs.sejob_jobtitle');
        $this->db->from('sejobapplicant');
        $this->db->join('secandidates', 'sejobapplicant.candidate_id = secandidates.id', 'left');
        $this->db->join('sejobs', 'sejobapplicant.job_id = sejobs.sejob_id', 'left');

        // Updated: Look for both specific interview rounds
        $this->db->where_in('sejoba_state', ['technical interview', 'communication and document verification']);

        $this->db->order_by('sejoba_interview_date', 'ASC');
        return $this->db->get()->result();
    }

    public function get_new_applicants_count()
    {
        return $this->db->where('sejoba_state', 'applied')->count_all_results('sejobapplicant');
    }

    public function get_recent_applicants($limit = 5)
    {
        $this->db->select('
            sejobapplicant.*, 
            secandidates.full_name AS sejoba_name, 
            sejobs.sejob_jobtitle AS sejoba_position
        ');
        $this->db->from('sejobapplicant');
        $this->db->join('secandidates', 'sejobapplicant.candidate_id = secandidates.id', 'left');
        $this->db->join('sejobs', 'sejobapplicant.job_id = sejobs.sejob_id', 'left');
        $this->db->order_by('sejobapplicant.sejoba_atime', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result_array();
    }
    // Submits the finalized application into the bridging table
    public function submit_application($candidate_id, $job_id, $resume_path, $cover_letter, $phone, $experience, $expected_salary)
    {
        $data = array(
            'candidate_id' => $candidate_id,
            'job_id' => $job_id,
            'sejoba_phone' => $phone,
            'sejoba_experience' => $experience,
            'sejoba_exp_salary' => $expected_salary,
            'sejoba_resume' => $resume_path,
            'sejoba_coverletter' => $cover_letter,
            'sejoba_state' => 'applied',
            'sejoba_atime' => date('Y-m-d H:i:s')
        );

        $this->db->trans_start();
        $this->db->insert('sejobapplicant', $data);
        $new_id = $this->db->insert_id(); // Capture the new application ID
        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE) {
            // TRIGGER EMAIL LOGIC
            $this->db->select('secandidates.email, secandidates.full_name, sejobs.sejob_jobtitle');
            $this->db->from('sejobapplicant');
            $this->db->join('secandidates', 'sejobapplicant.candidate_id = secandidates.id', 'left');
            $this->db->join('sejobs', 'sejobapplicant.job_id = sejobs.sejob_id', 'left');
            $this->db->where('sejobapplicant.sejoba_id', $new_id);

            $details = $this->db->get()->row();

            if ($details) {
                $this->load->model('EmailModel');
                $this->EmailModel->send_applicant_submission_email(
                    $details->email,
                    $details->full_name,
                    $details->sejob_jobtitle,
                    $phone
                );
            }
            return TRUE;
        }
        return FALSE;
    }

    public function is_eligible_to_apply($candidate_id)
    {
        $this->db->where('candidate_id', $candidate_id);
        $this->db->where('sejoba_state !=', 'rejected');
        $query = $this->db->get('sejobapplicant');
        return $query->num_rows() == 0;
    }

    public function get_applications_by_candidate($candidate_id)
    {
        $this->db->select('
            sejobapplicant.*, 
            sejobs.sejob_jobtitle, 
            sejobs.sejob_address,
            secandidates.email as candidate_email
        ');
        $this->db->from('sejobapplicant');
        $this->db->join('sejobs', 'sejobapplicant.job_id = sejobs.sejob_id', 'left');
        $this->db->join('secandidates', 'sejobapplicant.candidate_id = secandidates.id', 'left');
        $this->db->where('sejobapplicant.candidate_id', $candidate_id);
        $this->db->order_by('sejobapplicant.sejoba_atime', 'DESC');

        return $this->db->get()->result();
    }

}