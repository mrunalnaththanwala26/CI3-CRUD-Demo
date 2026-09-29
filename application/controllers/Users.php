<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller
{
    private $upload_path = './uploads/profiles/';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('pagination');
    }

    /* ---------- READ (list + search + pagination) ---------- */
    public function index($offset = 0)
    {
        $search   = trim((string) $this->input->get('q', TRUE));
        $per_page = 5;

        $config['base_url']             = site_url('users/index');
        $config['total_rows']           = $this->User_model->count_all($search);
        $config['per_page']             = $per_page;
        $config['reuse_query_string']   = TRUE;
        $config['full_tag_open']        = '<ul class="pagination">';
        $config['full_tag_close']       = '</ul>';
        $config['num_tag_open']         = '<li>';
        $config['num_tag_close']        = '</li>';
        $config['cur_tag_open']         = '<li class="active"><a>';
        $config['cur_tag_close']        = '</a></li>';
        $config['next_tag_open']        = $config['prev_tag_open'] = $config['first_tag_open'] = $config['last_tag_open'] = '<li>';
        $config['next_tag_close']       = $config['prev_tag_close'] = $config['first_tag_close'] = $config['last_tag_close'] = '</li>';
        $this->pagination->initialize($config);

        $data['title']  = 'Users';
        $data['users']  = $this->User_model->get_all($search, $per_page, (int) $offset);
        $data['search'] = $search;
        $data['links']  = $this->pagination->create_links();
        $this->render('users/index', $data);
    }

    /* ---------- CREATE ---------- */
    public function create()
    {
        $this->set_rules();
        if ($this->form_validation->run() === FALSE) {
            $data['title']       = 'Add User';
            $data['user']        = NULL;
            $data['hobbies']     = $this->User_model->all_hobbies();
            $data['my_hobbies']  = (array) $this->input->post('hobbies');
            $data['upload_error'] = '';
            return $this->render('users/form', $data);
        }

        $pic = $this->handle_upload();
        if ($pic === FALSE) {
            $data['title']        = 'Add User';
            $data['user']         = NULL;
            $data['hobbies']      = $this->User_model->all_hobbies();
            $data['my_hobbies']   = (array) $this->input->post('hobbies');
            $data['upload_error'] = $this->upload->display_errors('', '');
            return $this->render('users/form', $data);
        }

        $row = $this->collect_input();
        $row['profile_pic'] = $pic;
        $id = $this->User_model->insert($row, $this->input->post('hobbies'));

        if ($id) {
            $mail_ok = $this->send_welcome_mail($row['name'], $row['email']);
            $msg = 'User created successfully.' . ($mail_ok ? ' Welcome email sent.' : ' (Welcome email could not be sent - check config/email.php)');
            $this->session->set_flashdata('success', $msg);
        } else {
            $this->session->set_flashdata('error', 'Could not create user.');
        }
        redirect('users');
    }

    /* ---------- UPDATE ---------- */
    public function edit($id)
    {
        $user = $this->User_model->get($id);
        if (!$user) show_404();

        $this->set_rules($id);
        if ($this->form_validation->run() === FALSE) {
            return $this->edit_form($user, '');
        }

        $pic = $this->handle_upload();
        if ($pic === FALSE) {
            return $this->edit_form($user, $this->upload->display_errors('', ''));
        }

        $row = $this->collect_input();
        if ($pic !== NULL) {                      // new file uploaded -> replace old one
            $this->delete_file($user->profile_pic);
            $row['profile_pic'] = $pic;
        }
        $this->User_model->update($id, $row, $this->input->post('hobbies'));
        $this->session->set_flashdata('success', 'User updated successfully.');
        redirect('users');
    }

    private function edit_form($user, $upload_error)
    {
        $data['title']        = 'Edit User';
        $data['user']         = $user;
        $data['hobbies']      = $this->User_model->all_hobbies();
        $data['my_hobbies']   = $this->input->post()
                                ? (array) $this->input->post('hobbies')
                                : $this->User_model->user_hobby_ids($user->id);
        $data['upload_error'] = $upload_error;
        $this->render('users/form', $data);
    }

    /* ---------- DELETE (POST only) ---------- */
    public function delete($id)
    {
        if ($this->input->method() !== 'post') show_error('Method not allowed', 405);

        $user = $this->User_model->get($id);
        if ($user) {
            $this->delete_file($user->profile_pic);
            $this->User_model->delete($id);
            $this->session->set_flashdata('success', 'User deleted.');
        }
        redirect('users');
    }

    /* ---------- VIEW single user ---------- */
    public function view($id)
    {
        $user = $this->User_model->get($id);
        if (!$user) show_404();
        $ids  = $this->User_model->user_hobby_ids($id);
        $data['title']      = 'User Details';
        $data['user']       = $user;
        $data['hobby_names'] = array_map(function ($h) { return $h->name; },
                                array_filter($this->User_model->all_hobbies(), function ($h) use ($ids) {
                                    return in_array((int) $h->id, $ids);
                                }));
        $this->render('users/view', $data);
    }

    /* ---------- SEND MAIL ---------- */
    public function send_mail($id)
    {
        $user = $this->User_model->get($id);
        if (!$user) show_404();

        $this->form_validation->set_rules('subject', 'Subject', 'required|trim|max_length[150]');
        $this->form_validation->set_rules('message', 'Message', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Send Email';
            $data['user']  = $user;
            return $this->render('users/mail', $data);
        }

        $ok = $this->send_mail_to(
            $user->email,
            $this->input->post('subject', TRUE),
            nl2br(html_escape($this->input->post('message')))
        );
        $this->session->set_flashdata($ok ? 'success' : 'error',
            $ok ? 'Email sent to ' . $user->email : 'Email failed. Check SMTP settings in config/email.php');
        redirect('users');
    }

    /* =================== helpers =================== */
    private function set_rules($id = NULL)
    {
        $email_rule = 'required|trim|valid_email|max_length[150]|is_unique[users.email' . ($id ? '.id.' . (int) $id : '') . ']';
        $this->form_validation->set_rules('name',   'Name',   'required|trim|min_length[2]|max_length[100]');
        $this->form_validation->set_rules('email',  'Email',  $email_rule);
        $this->form_validation->set_rules('phone',  'Phone',  'trim|regex_match[/^[0-9+\-\s]{7,20}$/]');
        $this->form_validation->set_rules('gender', 'Gender', 'required|in_list[male,female,other]');
        $this->form_validation->set_rules('hobbies[]', 'Hobbies', 'trim');
    }

    private function collect_input()
    {
        return [
            'name'   => $this->input->post('name', TRUE),
            'email'  => $this->input->post('email', TRUE),
            'phone'  => $this->input->post('phone', TRUE),
            'gender' => $this->input->post('gender', TRUE),
        ];
    }

    /**
     * Returns: filename (uploaded) | NULL (no file chosen) | FALSE (upload error)
     */
    private function handle_upload()
    {
        if (empty($_FILES['profile_pic']['name'])) return NULL;

        $config['upload_path']   = $this->upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png|gif';
        $config['max_size']      = 2048;          // KB
        $config['encrypt_name']  = TRUE;          // random name, avoids clashes/guessing
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('profile_pic')) return FALSE;
        return $this->upload->data('file_name');
    }

    private function delete_file($name)
    {
        if ($name && is_file($this->upload_path . $name)) @unlink($this->upload_path . $name);
    }

    private function send_welcome_mail($name, $email)
    {
        $body = '<h3>Welcome, ' . html_escape($name) . '!</h3>'
              . '<p>Your profile has been created successfully.</p>';
        return $this->send_mail_to($email, 'Welcome to CI3 CRUD App', $body);
    }

    private function send_mail_to($to, $subject, $html)
    {
        $this->load->config('email');                 // loads config/email.php
        $this->load->library('email');
        $this->email->from($this->config->item('from_email'), $this->config->item('from_name'));
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($html);

        $ok = $this->email->send(FALSE);
        if (!$ok) log_message('error', $this->email->print_debugger(['headers']));
        return $ok;
    }

    private function render($view, $data)
    {
        $this->load->view('layout/header', $data);
        $this->load->view($view, $data);
        $this->load->view('layout/footer');
    }
}
