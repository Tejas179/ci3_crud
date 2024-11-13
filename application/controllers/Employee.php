<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Employee_model');
        $this->load->library('form_validation');
    }

    public function index() {
        $data['employees'] = $this->Employee_model->get_all_employees();
        $this->load->view('employee/create', $data);
    }

    public function create() {
        $this->load->view('employee/create');
    }

    public function store() {
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('mobile', 'Mobile', 'required|numeric');
        $this->form_validation->set_rules('address', 'Address', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('employee/create');
        } else {
            $config['upload_path'] = './image/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('profile_image')) {
                $upload_data = $this->upload->data();
                $data = array(
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'mobile' => $this->input->post('mobile'),
                    'address' => $this->input->post('address'),
                    'profile_image' => 'image/' . $upload_data['file_name']
                );

                $this->Employee_model->insert_employee($data);
                $this->session->set_flashdata('success', 'Employee added successfully');
                redirect('employee');
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                $this->load->view('employee/create');
            }
        }
    }
    public function get_employee() {
        $id = $this->input->post('id'); // Get `id` from POST data
    
        if ($id) {
            $employee = $this->Employee_model->get_employee_by_id($id);
            if ($employee) {
                echo json_encode($employee);
            } else {
                echo json_encode(['error' => 'Employee not found']);
            }
        } else {
            echo json_encode(['error' => 'Invalid request']);
        }
    }
    
    
    public function update() {
        $id = $this->input->post('id');
        $data = array(
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'mobile' => $this->input->post('mobile'),
            'address' => $this->input->post('address')
        );
    
        if (!empty($_FILES['profile_image']['name'])) {
            $config['upload_path'] = './image/';
            $config['allowed_types'] = 'jpg|jpeg|png';
            $this->load->library('upload', $config);
            if ($this->upload->do_upload('profile_image')) {
                $upload_data = $this->upload->data();
                $data['profile_image'] = 'image/' . $upload_data['file_name'];
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors());
                redirect('employee');
            }
        } else {
            $data['profile_image'] = $this->input->post('existing_image');
        }
    
        $this->Employee_model->update_employee($id, $data);
        $this->session->set_flashdata('success', 'Employee updated successfully');
        redirect('employee');
    }
    public function delete($id) {
        if ($this->Employee_model->delete_employee($id)) {
            $this->session->set_flashdata('success', 'Employee deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete employee');
        }
        redirect('employee');
    }
    
}
?>
