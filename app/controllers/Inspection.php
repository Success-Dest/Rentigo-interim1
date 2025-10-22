<?php
class Inspection extends Controller
{
    private $M_Inspection;

    public function __construct()
    {
        $this->M_Inspection = $this->model('M_Inspection');
    }

    // Show all inspections
    public function index()
    {
        $inspections = $this->M_Inspection->getInspections();
        $data = [
            'title' => 'Property Inspections',
            'page' => 'inspections',
            'inspections' => $inspections,
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('manager/v_inspections', $data);
    }

    // Add inspection
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'property' => trim($_POST['property']),
                'type' => trim($_POST['type']),
                'issues' => ($_POST['issues'] === '' ? 0 : (int) $_POST['issues']),
                'date' => trim($_POST['date']),
                'error' => ''
            ];

            // Business logic: date must be in the future
            $selectedDate = strtotime($data['date']);
            $today = strtotime(date('Y-m-d'));

            if ($selectedDate <= $today) {
                $data['error'] = 'The inspection date must be a future date.';
                // Re-render the form with the error message
                $data['title'] = 'Schedule Inspection';
                $data['page'] = 'add_inspection';
                $data['user_name'] = $_SESSION['user_name'];
                return $this->view('manager/v_add_inspection', $data);
            }

            // Attempt to add inspection
            $inserted = $this->M_Inspection->addInspection($data);

            if ($inserted) {
                redirect('inspection/index');
            } else {
                echo '<h3 style="color:red;">Failed to save inspection. Check database connection, table structure, or input values.</h3>';
            }
        } else {
            // GET request → show form
            $data = [
                'title' => 'Schedule Inspection',
                'page' => 'add_inspection',
                'user_name' => $_SESSION['user_name']
            ];
            $this->view('manager/v_add_inspection', $data);
        }
    }


    // Edit inspection
    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'id' => $id,
                'property' => trim($_POST['property']),
                'type' => trim($_POST['type']),
                'date' => trim($_POST['date']),
                'status' => trim($_POST['status']),
                'issues' => ($_POST['issues'] === '' ? 0 : (int) $_POST['issues'])
            ];

            $updated = $this->M_Inspection->updateInspection($id, $data);

            if ($updated) {
                redirect('inspection/index');
            } else {
                echo '<h3 style="color:red;">Failed to update inspection. Check database connection, table structure, or input values.</h3>';
            }
        } else {
            $inspection = $this->M_Inspection->getInspectionById($id);
            if (!$inspection) {
                die('Inspection not found');
            }

            $data = [
                'title' => 'Edit Inspection',
                'page' => 'edit_inspection',
                'inspection' => $inspection,
                'user_name' => $_SESSION['user_name']
            ];
            $this->view('manager/v_edit_inspection', $data);
        }
    }
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($this->M_Inspection->deleteInspection($id)) {
                redirect('inspection/index');
            } else {
                echo '<h3 style="color:red;">Failed to delete inspection. Try again.</h3>';
            }
        } else {
            die('Invalid request method');
        }
    }
}
