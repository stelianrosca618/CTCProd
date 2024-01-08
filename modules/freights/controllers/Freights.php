<?php

use app\services\utilities\Date;
use app\services\tasks\TasksKanban;

defined('BASEPATH') or exit('No direct script access allowed');

class Freights extends AdminController
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('freights_model');
    }

    /* Open also all taks if user access this /tasks url */
    public function index($id = '')
    {
        $this->list_freights($id);
    }

    /* List all freights */
    public function list_freights($id = '')
    {
        close_setup_menu();
        // If passed from url
        $data['custom_view'] = $this->input->get('custom_view') ? $this->input->get('custom_view') : '';
        $data['taskid']      = $id;

        $data['switch_kanban'] = false;
        $data['bodyclass']     = 'freights-page';

        $data['title'] = _l('freights');

        $this->app_scripts->add('freights-js', module_dir_url('freights', 'assets/js/freights.js'), 'admin', ['app-js']);

        $this->load->view('manage', $data);
    }

    public function table()
    {
        $this->app->get_table_data(module_views_path('freights', 'tables/freights'));
        // $this->app->get_table_data('freights');
    }

    /* Add new freight or update existing */
    public function freight($id = '')
    {
        if (!has_permission('freights', '', 'edit') && !has_permission('freights', '', 'create')) {
            ajax_access_denied();
        }

        $data = [];

        if ($this->input->post()) {
            $data                = $this->input->post();
            if ($id == '') {
                if (!has_permission('freights', '', 'create')) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                        'success' => false,
                        'message' => _l('access_denied'),
                    ]);
                    die;
                }
                $id      = $this->freights_model->add($data);
                $_id     = false;
                $success = false;
                $message = '';
                if ($id) {
                    $success       = true;
                    $_id           = $id;
                    $message       = _l('added_successfully', _l('freight'));
                }
                echo json_encode([
                    'success' => $success,
                    'id'      => $_id,
                    'message' => $message,
                ]);
            } else {
                if (!has_permission('freights', '', 'edit')) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode([
                        'success' => false,
                        'message' => _l('access_denied'),
                    ]);
                    die;
                }
                $success = $this->freights_model->update($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('freight'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                    'id'      => $id,
                ]);
            }
            die;
        }

        if ($id == '') {
            $title = _l('add_new', _l('freight_lowercase'));
        } else {
            $data['freight'] = $this->freights_model->get($id);
            $title = _l('edit', _l('freight_lowercase')) . ' ' . $data['freight']->name;
        }
        
        $data['id']      = $id;
        $data['title']   = $title;
        $this->load->view('freight', $data);
    }

    /**
     * Freight ajax request modal
     * @param  mixed $taskid
     * @return mixed
     */
    public function get_freight_data($freightid, $return = false)
    {
        $freights_where = [];

        $freight = $this->freights_model->get($freightid);

        if (!$freight) {
            header('HTTP/1.0 404 Not Found');
            echo 'Task not found';
            die();
        }

        $data['freight']               = $freight;
        $data['id']                 = $freight->id;

        if ($return == false) {
            $this->load->view('view_freight_template', $data);
        } else {
            return $this->load->view('view_freight_template', $data, true);
        }
    }

    /* Delete freight from database */
    public function delete_freight($id)
    {
        if (!has_permission('freights', '', 'delete')) {
            access_denied('freights');
        }
        $success = $this->freights_model->delete($id);
        $message = _l('problem_deleting', _l('freight_lowercase'));
        if ($success) {
            $message = _l('deleted', _l('freight'));
            set_alert('success', $message);
        } else {
            set_alert('warning', $message);
        }

        redirect($_SERVER['HTTP_REFERER']);
    }
}
