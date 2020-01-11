<?php 
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
	$json = file_get_contents('php://input');
	$data = json_decode($json);
    // Check if username is empty
	if(isset($data->username)){
      if(empty($data->username)){
        echo "username not found";
		exit;
      } else{
        $username = $data->username;
	  }
    } else {
        echo "username not found";
		exit;		
	}
	if(isset($data->password)){
      if(empty($data->password)){
        echo "password not found";
		exit;
      } else{
        $password = $data->password;
      }
    } else {
        echo "password not found";
		exit;		
	}
	if(isset($data->title)){
      if(empty($data->title)){
        echo "title not found";
		exit;
      } else{
        $title = $data->title;
	  }
    } else {
        echo "title not found";
		exit;		
	}
	if(isset($data->link)){
      $llink = $data->link;  // Beware link is connection variable
    } else {
        echo "link not found";
		exit;		
	}
	if(isset($data->description)){
      $description = $data->description;
    } else {
        echo "description not found";
		exit;		
	}
	if(isset($data->pubdate)){
      if(empty($data->pubdate)){
        echo "pubdate not found";
		exit;
      } else{
        $pubdate = $data->pubdate;
	  }
    } else {
        echo "pubdate not found";
		exit;		
	}

    // Prepare a select statement
    $sql = "SELECT id, username, password FROM users WHERE username = ?";
	if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
	}
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                                                        
                        } else{
                            // Display an error message if password is not valid
                            echo "password not valid";
							exit;
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    echo "username not found";
					exit;
                }
				// Close statement
				mysqli_stmt_close($stmt);
								
				// Prepare an insert statement
				$sql = "INSERT INTO news (title, link, description, pubdate) VALUES (?, ?, ?, ?)";
         
				if($stmt = mysqli_prepare($link, $sql)){
					// Bind variables to the prepared statement as parameters
					mysqli_stmt_bind_param($stmt, "ssss", $param_title, $param_link, $param_description, $param_pubdate);
					
					// Set parameters
					$param_title = $title;
					$param_link = $llink;
					$param_description = $description;
					$param_pubdate = $pubdate;
            
					// Attempt to execute the prepared statement
					if(mysqli_stmt_execute($stmt)){
						echo "post success";
					} else{
						echo "try again later";
					}
				}
				// Close statement
				mysqli_stmt_close($stmt);
            } else{
                echo "try again later";
				// Close statement
				mysqli_stmt_close($stmt);
				exit;
            }
        }
        		    
    // Close connection
    mysqli_close($link);
}
?>
