<?php
require_once '../app/helpers/helper.php';

class Landlord extends Controller
{
    private $userModel;
    private $propertyModel;
    private $maintenanceModel;
    private $rentOptimizer;  // Rent Optimizer

    public function __construct()
    {
        // Check if user is logged in and is a landlord
        if (!isLoggedIn() || $_SESSION['user_type'] !== 'landlord') {
            redirect('users/login');
        }

        // Initialize models
        $this->userModel = $this->model('M_Users');
        $this->propertyModel = $this->model('M_Properties');
        $this->rentOptimizer = $this->model('M_RentOptimizer');  // ✨ NEW
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $data = [
            'title' => 'Landlord Dashboard',
            'page' => 'dashboard',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_dashboard', $data);
    }

    public function properties()
    {
        $properties = $this->propertyModel->getPropertiesByLandlord($_SESSION['user_id']);
        $data = [
            'properties' => $properties
        ];
        $this->view('landlord/v_properties', $data);
    }

    public function add_property()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            // Map pet policy and laundry values to match database ENUM
            $petOptions = ['no', 'cats', 'dogs', 'both'];
            $laundryOptions = ['none', 'shared', 'hookups', 'in_unit', 'included'];

            $data = [
                'landlord_id'   => $_SESSION['user_id'],
                'address'       => trim($_POST['address']),
                'property_type' => trim($_POST['type']),
                'bedrooms'      => (int)$_POST['bedrooms'],
                'bathrooms'     => (float)$_POST['bathrooms'],
                'sqft'          => (int)$_POST['sqft'],
                'rent'          => (float)$_POST['rent'],
                'deposit'       => (float)$_POST['deposit'],
                'available_date' => !empty($_POST['available_date']) ? $_POST['available_date'] : null,
                'parking'       => isset($_POST['parking']) && is_numeric($_POST['parking']) ? (int)$_POST['parking'] : 0,
                'pet_policy'    => in_array($_POST['pets'], $petOptions) ? $_POST['pets'] : 'no',
                'laundry'       => in_array($_POST['laundry'], $laundryOptions) ? $_POST['laundry'] : 'none',
                'description'   => trim($_POST['description']),
                'status'        => 'vacant'
            ];

            if ($this->propertyModel->addProperty($data)) {
                flash('property_message', 'Property Added Successfully');
                redirect('landlord/properties');
            } else {
                die('Something went wrong.');
            }
        } else {
            // Initialize empty data for the form
            $data = [
                'title' => 'Add Property',
                'address' => '',
                'type' => '',
                'bedrooms' => '',
                'bathrooms' => '',
                'sqft' => '',
                'rent' => '',
                'deposit' => '',
                'available_date' => '',
                'parking' => '0',
                'pets' => 'no',
                'laundry' => 'none',
                'description' => '',
                'address_err' => '',
                'type_err' => '',
                'bedrooms_err' => '',
                'bathrooms_err' => '',
                'rent_err' => ''
            ];

            $this->view('landlord/v_add_property', $data);
        }
    }

    /**
     *  NEW METHOD: Suggest Rent (AJAX Endpoint)
     * Called when landlord clicks "Get Smart Rent Suggestion" button
     */
    public function suggestRent()
    {
        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            redirect('landlord/properties');
        }

        // Set JSON header
        header('Content-Type: application/json');

        try {
            // Get and sanitize form data
            $propertyData = [
                'address' => trim($_POST['address'] ?? ''),
                'property_type' => trim($_POST['property_type'] ?? ''),
                'bedrooms' => (int)($_POST['bedrooms'] ?? 0),
                'bathrooms' => (float)($_POST['bathrooms'] ?? 0),
                'sqft' => !empty($_POST['sqft']) ? (int)$_POST['sqft'] : null,
                'parking' => trim($_POST['parking'] ?? '0'),
                'pet_policy' => trim($_POST['pet_policy'] ?? 'no'),
                'laundry' => trim($_POST['laundry'] ?? 'none')
            ];

            // Validate required fields
            if (empty($propertyData['property_type']) || empty($propertyData['bedrooms'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Property type and bedrooms are required to generate a suggestion.'
                ]);
                exit;
            }

            // Get rent suggestion from model
            $suggestion = $this->rentOptimizer->suggestRent($propertyData);

            // Return suggestion as JSON
            echo json_encode($suggestion);
            exit;
        } catch (Exception $e) {
            // Log error for debugging
            error_log('Rent suggestion error: ' . $e->getMessage());

            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while analyzing market data. Please try again or enter rent manually.'
            ]);
            exit;
        }
    }

    public function edit($id)
    {
        $property = $this->propertyModel->getPropertyById($id);

        if (!$property) {
            flash('property_message', 'Property not found');
            redirect('landlord/properties');
        }

        $data = ['property' => $property];

        $this->view('landlord/v_edit_properties', $data);
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

            // Ensure only valid ENUM values for pet_policy and laundry
            $validPetPolicies = ['no', 'cats', 'dogs', 'both'];
            $validLaundry = ['none', 'shared', 'hookups', 'in_unit', 'included'];

            $data = [
                'id' => $id,
                'address' => trim($_POST['address']),
                'property_type' => trim($_POST['property_type']),
                'bedrooms' => (int)$_POST['bedrooms'],
                'bathrooms' => (float)$_POST['bathrooms'],
                'sqft' => (int)$_POST['sqft'],
                'rent' => (float)$_POST['rent'],
                'deposit' => (float)$_POST['deposit'],
                'available_date' => !empty($_POST['available_date']) ? $_POST['available_date'] : null,
                'parking' => trim($_POST['parking']),
                'pet_policy' => in_array($_POST['pets'] ?? '', $validPetPolicies) ? $_POST['pets'] : 'no',
                'laundry' => in_array($_POST['laundry'] ?? '', $validLaundry) ? $_POST['laundry'] : 'none',
                'description' => trim($_POST['description']),
            ];

            if ($this->propertyModel->update($data)) {
                flash('property_message', 'Property Updated Successfully');
                redirect('landlord/properties');
            } else {
                die('Something went wrong.');
            }
        } else {
            $property = $this->propertyModel->getPropertyById($id);
            if (!$property) {
                flash('property_message', 'Property not found');
                redirect('landlord/properties');
            }
            $data = ['property' => $property];
            $this->view('landlord/v_edit_properties', $data);
        }
    }

    public function delete($id)
    {
        if ($this->propertyModel->deleteProperty($id)) {
            flash('property_message', 'Property removed');
            redirect('landlord/properties');
        } else {
            die('Something went wrong');
        }
    }

    public function maintenance()
    {
        $data = [
            'title' => 'Maintenance Requests',
            'page' => 'maintenance',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_maintenance', $data);
    }

    public function inquiries()
    {
        $data = [
            'title' => 'Tenant Inquiries',
            'page' => 'inquiries',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_inquiries', $data);
    }

    public function payment_history()
    {
        $data = [
            'title' => 'Payment History',
            'page' => 'payment_history',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_payment_history', $data);
    }

    public function feedback()
    {
        $data = [
            'title' => 'Tenant Feedback',
            'page' => 'feedback',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_feedback', $data);
    }

    public function notifications()
    {
        $data = [
            'title' => 'Notifications',
            'page' => 'notifications',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_notifications', $data);
    }

    public function settings()
    {
        $data = [
            'title' => 'Settings',
            'page' => 'settings',
            'user_name' => $_SESSION['user_name']
        ];
        $this->view('landlord/v_settings', $data);
    }

    public function income()
    {
        $data = [
            'title' => 'Income Reports',
            'page' => 'income',
            'user_name' => $_SESSION['user_name']
        ];

        $this->view('landlord/v_income', $data);
    }

    // Show new maintenance request form
    public function new_maintenance_request()
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            redirect('users/login');
        }

        // Get landlord's properties for dropdown
        // $properties = $this->propertyModel->getPropertiesByLandlordId($_SESSION['user_id']);

        $data = [
            'title' => 'New Maintenance Request - Rentigo',
            // 'properties' => $properties,
            'property_id' => '',
            'request_title' => '',
            'category' => '',
            'priority' => 'medium',
            'description' => '',
            'tenant_name' => '',
            'tenant_contact' => '',
            'access_instructions' => '',
            'property_err' => '',
            'title_err' => '',
            'category_err' => '',
            'priority_err' => '',
            'description_err' => ''
        ];

        $this->view('landlord/v_new_maintenance_request', $data);
    }

    // Process maintenance request submission
    public function create_maintenance_request()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            $data = [
                'property_id' => trim($_POST['property_id']),
                'title' => trim($_POST['title']),
                'category' => trim($_POST['category']),
                'priority' => trim($_POST['priority']),
                'description' => trim($_POST['description']),
                'tenant_name' => trim($_POST['tenant_name']),
                'tenant_contact' => trim($_POST['tenant_contact']),
                'access_instructions' => trim($_POST['access_instructions']),
                'property_err' => '',
                'title_err' => '',
                'category_err' => '',
                'priority_err' => '',
                'description_err' => ''
            ];

            // Validate property
            if (empty($data['property_id'])) {
                $data['property_err'] = 'Please select a property';
            }

            // Validate title
            if (empty($data['title'])) {
                $data['title_err'] = 'Please enter a title';
            }

            // Validate category
            if (empty($data['category'])) {
                $data['category_err'] = 'Please select a category';
            }

            // Validate priority
            if (empty($data['priority'])) {
                $data['priority_err'] = 'Please select a priority level';
            }

            // Validate description
            if (empty($data['description'])) {
                $data['description_err'] = 'Please provide a description';
            }

            // Check for errors
            if (
                empty($data['property_err']) && empty($data['title_err']) &&
                empty($data['category_err']) && empty($data['priority_err']) &&
                empty($data['description_err'])
            ) {
                // Create maintenance request
                if ($this->maintenanceModel->create($data)) {
                    flash('maintenance_message', 'Maintenance request submitted successfully!', 'alert alert-success');
                    redirect('landlord/maintenance');
                } else {
                    die('Something went wrong');
                }
            } else {
                // Load view with errors
                $properties = $this->propertyModel->getPropertiesByLandlordId($_SESSION['user_id']);
                $data['properties'] = $properties;
                $this->view('landlord/v_new_maintenance_request', $data);
            }
        } else {
            redirect('landlord/new_maintenance_request');
        }
    }
}
