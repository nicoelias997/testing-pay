<?php

return [
    'home' => 'Home',
    'login' => 'Log in',
    'register' => 'Register',
    'buttons' => [
        'submit' => 'Submit'
    ],
    'form_texts' => [
        'order' => 'Enter the order number to be searched and processed.'
    ],
    'order_number' => 'Order number',
    'error' => 'An error has occurred in the application. Please contact the application administrator to resolve it.',
    'errors' => [
        'order_exists_mes' => 'Order :order was not found in the MES system.',
        'machine_exists_mes' => 'Machine :machine could not be assigned for order :order because it does not exist in the MES system.',
        'duplicated_order_mes' => 'Order :order could not be loaded as it already exists in the MES system.',
        'process' => '{1} Order :id could not be processed.|[2,*] Orders were not processed.',
        'invalid_access_permission' => 'You do not have permission to access any management panels. Please contact the application administrator to resolve it.',
    ],
    'auth' => [
        'invalid' => 'Invalid credentials'
    ],
    // Replace with 'errors'
    'invalid_access_permission' => 'You do not have permission to access any management panels. Please contact the application administrator to resolve it.',
    'model' => [
        'created'       => 'Record created successfully.',
        'updated'       => 'Record updated successfully.',
        'deleted'       => 'Record deleted successfully.',
        // Unused?
        'error'         => 'An error occurred while applying action(s) to the record.',
        'error_message' => 'An error occurred while applying action(s) to the record: :message',
    ],
    'order' => [
        'created'       => 'Order created successfully.',
        'updated'       => 'Order updated successfully.',
        'deleted'       => 'Order deleted successfully.',
        // Unused?
        'error'         => 'An error occurred while applying action(s) to the order.',
        'error_message' => 'An error occurred while applying action(s) to the order: :message',
    ],
    'process' => [
        'success' => '{1} Order :id processed successfully.|[2,*] Orders processed successfully.',
        'operations' => '{1} Order :id operations processed successfully.|[2,*] operations processed successfully.',
        // Replace with 'errors'
        'error'   => '{1} Order :id could not be processed.|[2,*] Orders were not processed.',
        
    ],
    'user' => [
        'created'       => 'User created successfully.',
        'updated'       => 'User updated successfully.',
        'deleted'       => 'User deleted successfully.',
        // Unused?
        'error'         => 'An error occurred while applying action(s) to the user.',
        'error_message' => 'An error occurred while applying action(s) to the user: :message',
    ],
    "categories" => [
        "calidad" => "Quality",
        "estampacion" =>  "Stamping",
        "decolataje" => "Turning",
        "limpieza" =>  "Cleaning Parts",
        "almacen" => "Warehouse",
        "produccion" => "Production",
        "roscadora" => "Threading",
        "ingenieria" => "Engineering",
        "almacenModula"=> "Modula Warehouse",
        "director"=> "Factory Director",
        "gerencia"=> "Management",
        "comercial"=> "Commercial Office",
        "logistica"=> "Logistics Office",
        "oficina"=> "Office",
        "gestionCalidad"=> "Quality Management",
        "calidadProcesos"=> "Quality Processes"
    ],
    "reason" => [
        "vacaciones" => "Vacation",
        "diaLibre" => "Day(s) Off",
        "otro" => "Other"
    ]
];
