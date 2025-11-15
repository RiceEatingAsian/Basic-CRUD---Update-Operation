<?php
// Start the session (though we're hardcoding the ID, good practice is to start)
session_start();

// ----------------------------------------------------
// Database Configuration
// ----------------------------------------------------
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "web_experiment_db";

// HARDCODED USER ID for this experiment
// In a real application, this would be retrieved from $_SESSION['user_id']
$user_id_to_edit = 1; 

// Initialize variables for the form
$user_name = '';
$user_email = '';
$message = '';

// Check for feedback messages after a submission attempt
if (isset($_SESSION['update_message'])) {
    $message = $_SESSION['update_message'];
    unset($_SESSION['update_message']); // Display once
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ----------------------------------------------------
// Fetch Existing User Data (READ Operation)
// ----------------------------------------------------
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("❌ Error preparing SELECT statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id_to_edit); // 'i' for integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $user_name = $user['name'];
    $user_email = $user['email'];
} else {
    $message = "❌ Error: User ID {$user_id_to_edit} not found.";
}

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Experiment 5: Edit Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); width: 350px; }
        h2 { text-align: center; color: #007bff; margin-bottom: 20px; }
        label { display: block; margin-top: 15px; font-weight: bold; color: #555; }
        input[type="text"], input[type="email"] { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { background-color: #28a745; color: white; padding: 12px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; margin-top: 25px; font-size: 16px; }
        button:hover { background-color: #218838; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🛠️ Edit Your Profile</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="process_update.php" method="POST">
            
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id_to_edit); ?>">
            
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_name); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required>

            <button type="submit" name="update_profile">Update Profile</button>
        </form>
    </div>
</body>
</html>