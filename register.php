<?php 
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'form';

    // Connect to the database
    $conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    // Check connection
    if(mysqli_connect_error()) {
        exit('Error connecting to the database: ' . mysqli_connect_error());
    }

    // Check if any field is empty
    if(empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
        exit('One or more fields are empty');
    }

    // Check if username already exists
    if($stmt = $conn->prepare('SELECT id FROM users WHERE username = ?')) {
        $stmt->bind_param('s', $_POST['username']);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0) {
            echo 'Username already exists. Please choose a different username.';
            $stmt->close();
            exit(); // Stop further execution
        }
    } else {
        exit('Error occurred while preparing username check statement: ' . $conn->error);
    }

    // Hash the password
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert new user into the database
    if($stmt = $conn->prepare('INSERT INTO users (username, password, email) VALUES (?, ?, ?)')) {
        $stmt->bind_param('sss', $_POST['username'], $password, $_POST['email']);
        if($stmt->execute()) {
            echo 'Successfully registered';
        } else {
            echo 'Error occurred while inserting user: ' . $stmt->error;
        }
    } else {
        echo 'Error occurred while preparing insert statement: ' . $conn->error;
    }

    // Close connection
    $conn->close();
?>
