<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CandidateModel extends CI_Model {

    // 1. Register a new manual candidate
    public function register_candidate($email, $password) 
    {
        $data = array(
            'email' => $email,
            // PHP's native password_hash is the most secure standard
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'oauth_provider' => 'manual'
        );
        
        $this->db->insert('secandidates', $data);
        
        // Return the new candidate's ID so the controller can log them in immediately
        return $this->db->insert_id(); 
    }

    // 2. Authenticate manual login
    public function login_candidate($email, $password) 
    {
        $this->db->where('email', $email);
        $query = $this->db->get('secandidates');

        if ($query->num_rows() == 1) {
            $user = $query->row();
            
            // Verify the hashed password against what they typed
            if (password_verify($password, $user->password_hash)) {
                return $user; // Success: Return the user object
            }
        }
        return FALSE; // Failed login
    }

    // 3. Check if an email already exists (Useful for registration validation)
    public function check_email_exists($email) 
    {
        $this->db->where('email', $email);
        $query = $this->db->get('secandidates');
        return $query->num_rows() > 0; // Returns TRUE if the email is taken
    }

    // 4. Fetch Candidate details by ID
    public function get_candidate_by_id($candidate_id) 
    {
        $this->db->where('id', $candidate_id);
        return $this->db->get('secandidates')->row();
    }

    // 5. Google OAuth Authentication Logic
     
    public function authenticate_google_user($oauth_uid, $email, $name = null) // <-- Added $name
    {
        // First, check if this Google ID already exists in our system
        $this->db->where('oauth_uid', $oauth_uid);
        $query = $this->db->get('secandidates');

        if ($query->num_rows() > 0) {
            return $query->row(); // User exists, return their data to log them in
        } 
        
        // If Google ID doesn't exist, check if they previously made a manual account with this email
        $this->db->where('email', $email);
        $email_query = $this->db->get('secandidates');
        
        if ($email_query->num_rows() > 0) {
            // Smooth UX: Link the Google account to their existing manual account
            $this->db->where('email', $email);
            $this->db->update('secandidates', array(
                'oauth_provider' => 'google', 
                'oauth_uid' => $oauth_uid,
                'full_name' => $name // Update their name if they didn't have one!
            ));
            return $email_query->row();
        }

        // If neither exists, they are a brand new user. Let's register them automatically.
        $data = array(
            'full_name' => $name, // <-- Save the name here
            'email' => $email,
            'oauth_provider' => 'google',
            'oauth_uid' => $oauth_uid
        );
        $this->db->insert('secandidates', $data);
        $new_id = $this->db->insert_id();

        // Return the newly created user object
        return $this->get_candidate_by_id($new_id); 
    }
}
?>