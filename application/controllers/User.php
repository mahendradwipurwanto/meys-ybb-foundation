<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    // construct
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['M_user', 'M_auth', 'M_announcements', 'M_master']);

        // cek apakah user sudah login
        if ($this->session->userdata('logged_in') == false || !$this->session->userdata('logged_in')) {
            if (!empty($_SERVER['QUERY_STRING'])) {
                $uri = uri_string() . '?' . $_SERVER['QUERY_STRING'];
            } else {
                $uri = uri_string();
            }
            $this->session->set_userdata('redirect', $uri);
            $this->session->set_flashdata('notif_warning', "Please login to continue");
            redirect('sign-in');
        }
    }

    public function index()
    {

        $data['user'] = $this->M_auth->get_auth($this->session->userdata('email'));
        // style
        $data['navbar_style']   = "navbar-dark";
        $data['logo_style']     = 1;
        $data['btn_sign_up']    = "btn-light";
        $data['btn_sign_in']    = "btn-outline-light";
        $data['participants']   = $this->M_user->getUserParticipans($this->session->userdata('user_id'));
        // ej($data['participants']->address);
        $this->templateuser->view('user/overview', $data);
    }

    public function documents()
    {
        $data['user'] = $this->M_auth->get_auth($this->session->userdata('email'));

        // style
        $data['navbar_style']   = "navbar-dark";
        $data['logo_style']     = 1;
        $data['btn_sign_up']    = "btn-light";
        $data['btn_sign_in']    = "btn-outline-light";

        $data['documents']  = $this->M_master->getDocuments();

        $this->templateuser->view('user/documents', $data);
    }

    public function announcements()
    {
        $data['user'] = $this->M_auth->get_auth($this->session->userdata('email'));

        // style
        $data['navbar_style']   = "navbar-dark";
        $data['logo_style']     = 1;
        $data['btn_sign_up']    = "btn-light";
        $data['btn_sign_in']    = "btn-outline-light";

        $data['announcements']  = $this->M_announcements->getParticipansAnnouncements();

        $this->templateuser->view('user/announcements', $data);
    }

    public function payment()
    {
        $data['user'] = $this->M_auth->get_auth($this->session->userdata('email'));

        // style
        $data['navbar_style']   = "navbar-dark";
        $data['logo_style']     = 1;
        $data['btn_sign_up']    = "btn-light";
        $data['btn_sign_in']    = "btn-outline-light";

        $this->templateuser->view('user/payment', $data);
    }

    public function submission()
    {
        $data['user'] = $this->M_auth->get_auth($this->session->userdata('email'));

        // style
        $data['navbar_style']   = "navbar-dark";
        $data['logo_style']     = 1;
        $data['btn_sign_up']    = "btn-light";
        $data['btn_sign_in']    = "btn-outline-light";
        
        $data['participants']   = $this->M_user->getUserParticipans($this->session->userdata('user_id'));
        $participans_id         = isset($data['participants']->id) ? $data['participants']->id : null;
        $data['p_essay']        = $this->M_user->getUserParticipansEssay($this->session->userdata('user_id'), $participans_id);
        $data['m_essay']        = $this->M_master->getParticipansEssay();
        $data['countries']      = $this->M_user->getAllCountries();

        $this->templateuser->view('user/submission', $data);
    }

    public function settings()
    {
        $data['user'] = $this->M_auth->get_auth($this->session->userdata('email'));

        // style
        $data['navbar_style']   = "navbar-dark";
        $data['logo_style']     = 1;
        $data['btn_sign_up']    = "btn-light";
        $data['btn_sign_in']    = "btn-outline-light";

        $this->templateuser->view('user/settings', $data);
    }

    public function changePassword()
    {
        $cur_password   = $this->input->post('currentPassword');
        $password       = $this->input->post('newPassword');
        $conf_password  = $this->input->post('confirmNewPassword');

        // mengambil data user dengan param email
        $user = $this->M_auth->get_auth($this->session->userdata('email'));
                // ej($user);

        if ($password == $conf_password) {
            //mengecek apakah password benar
            if (password_verify($cur_password, $user->password)) {
                if ($this->M_user->changePassword($password) == true) {

                    // atur dataemailperubahan password
                    $now = date("d F Y - H:i");
                    $email = htmlspecialchars($this->session->userdata("email"), true);

                    $subject = "Password change - Middle East Youth Summit";
                    $message = "Hi, password for Middle East Youth Summit account with email <b>{$email}</b> has been changed at {$now}. <br> If you feel you did not make these changes, please contact our admin immediately.";

                    // mengirimemailperubahan password
                    sendMail(htmlspecialchars($this->session->userdata("email"), true), $subject, $message);

                    $this->session->set_flashdata('notif_success', 'Password has been changes');
                    redirect($this->agent->referrer());
                } else {
                    $this->session->set_flashdata('notif_warning', 'There something wrong when try to changes your password');
                    redirect($this->agent->referrer());
                }
            } else {
                $this->session->set_flashdata('notif_warning', 'Password wrong');
                redirect($this->agent->referrer());
            }
        } else {
            $this->session->set_flashdata('notif_warning', 'Password doesn`t match');
            redirect($this->agent->referrer());
        }
    }
}