
<?php
// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user data
function getCurrentUser() {
    global $conn;
    if (!isLoggedIn()) {
        return null;
    }
    
    $id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Clean and validate input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Show success/error messages
function showMessage($message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Display the message
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'success';
        $class = ($type == 'error') ? 'alert-danger' : 'alert-success';
        
        echo '<div class="alert ' . $class . '">' . $_SESSION['message'] . '</div>';
        
        // Clear the message
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
}

// Redirect to a page
function redirect($page) {
    header("Location: $page");
    exit;
}

// Check user role
function checkRole($role) {
    $user = getCurrentUser();
    return $user && $user['role'] == $role;
}
?>
