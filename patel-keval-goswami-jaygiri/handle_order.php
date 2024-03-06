<?php

// Start the session
session_start();

// Function to generate a unique session ID
function generateSessionId() {
    return bin2hex(random_bytes(16));
}

// Function to calculate the total cost based on selected toppings
function calculateTotalCost($selectedToppings, $sessionId) {
    $toppingPrices = [
        'Pepperoni' => 1.50,
        'Mushrooms' => 1.00,
        'Green olives' => 1.00,
        'Green peppers' => 1.00,
        'Double cheese' => 2.25,
    ];

    $totalCost = 10.00;
    foreach ($selectedToppings as $topping) {
        if (isset($toppingPrices[$topping])) {
            $totalCost += $toppingPrices[$topping];
        }
    }

    // Update session variables with new total cost and selected toppings
    $_SESSION[$sessionId]['totalCost'] = $totalCost;
    $_SESSION[$sessionId]['selectedToppings'] = $selectedToppings;
}

// Function to create a new user session
function createNewUserSession($userInput) {
    $sessionId = generateSessionId();
    $_SESSION[$sessionId] = [
        'userInput' => $userInput,
        'selectedToppings' => [],
        'totalCost' => 10.00,
    ];

    return $sessionId;
}

// Function to update user session with selected toppings
function updateUserSession($sessionId, $selectedToppings) {
    $toppingPrices = [
        'Pepperoni' => 1.50,
        'Mushrooms' => 1.00,
        'Green olives' => 1.00,
        'Green peppers' => 1.00,
        'Double cheese' => 2.25,
    ];

    $totalCost = $_SESSION[$sessionId]['totalCost'];
    foreach ($selectedToppings as $topping) {
        $totalCost += $toppingPrices[$topping];
    }

    // Update session variables with new total cost and selected toppings
    $_SESSION[$sessionId]['selectedToppings'] = $selectedToppings;
    $_SESSION[$sessionId]['totalCost'] = $totalCost;
}

// Handle session destruction
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['destroySession'])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    echo json_encode(['status' => 'success', 'message' => 'Session destroyed']);
    exit();
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jsonData = file_get_contents('php://input');
    $postData = json_decode($jsonData, true);

    if (isset($postData['userInput'])) {
        // Handle the case when a new user session is created
        $userInput = $postData['userInput'];


         // Server-side validation for username
         if (empty($userInput) || !preg_match('/^[a-zA-Z ]+$/', $userInput)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid Username. Please Enter a Valid Name With Alphabetic Letters And Spaces.']);
            exit;
        }


        $sessionId = createNewUserSession($userInput);
        $toppingPrices = [
            'Pepperoni' => 1.50,
            'Mushrooms' => 1.00,
            'Green olives' => 1.00,
            'Green peppers' => 1.00,
            'Double cheese' => 2.25,
        ];
        echo json_encode(['status' => 'success', 'sessionId' => $sessionId, 'toppingPrices' => $toppingPrices]);
    } elseif (isset($postData['sessionId']) && isset($postData['selectedToppings'])) {
        // Handle the case when selected toppings are updated
        $sessionId = $postData['sessionId'];
        $selectedToppings = $postData['selectedToppings'];
        calculateTotalCost($selectedToppings, $sessionId);
        echo json_encode(['status' => 'success', 'sessionDetails' => $_SESSION[$sessionId]]);
    } elseif (isset($postData['sessionId'])) {
        // Handle the case when session details are requested
        $sessionId = $postData['sessionId'];
        echo json_encode(['status' => 'success', 'sessionDetails' => $_SESSION[$sessionId]]);
    } else {
        // Handle invalid request
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    }
} else {
    // Handle invalid request method
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
