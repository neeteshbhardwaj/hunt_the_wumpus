<?php
  $servername = "localhost";
  $username = "root";
  $db_name = "HUNT_THE_WUMPUS";
  $password = "";

  $no_of_rows = 5;
  $no_of_cols = 5;
  $no_of_initial_wumpuses = 5;

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $db_name);
  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

  $init_check_wumpuse = "SELECT 'INITIALIZED' INITIALIZED FROM WUMPUSES WHERE ROW_NUM = 2 AND COL_NUM = 2;";
  $init_check_wumpuse_result = mysqli_query($conn, $init_check_wumpuse);

  if(mysqli_affected_rows($conn) === 0) {
    $rand_row_num = 2;
    $rand_col_num = 2;

    for ($i=0; $i<$no_of_initial_wumpuses; $i++) {
      $init_insert_wumpuse = "INSERT INTO WUMPUSES(ROW_NUM, COL_NUM) VALUES($rand_row_num, $rand_col_num);";
      $init_insert_wumpuse_result = mysqli_query($conn, $init_insert_wumpuse);

      if(mysqli_affected_rows($conn) != 1) {
        echo "Error occurred: " . mysqli_connect_error();
      }

      $rand_row_num = mt_rand(0, $no_of_rows - 1);
      $rand_col_num = mt_rand(0, $no_of_cols - 1);

      while($rand_row_num === 0 && $rand_col_num === 0) {
          $rand_col_num = mt_rand(0, $no_of_cols - 1);
      }
    }
  }

?>