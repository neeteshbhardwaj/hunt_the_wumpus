<?php
$servername = "localhost";
$username = "root";
$db_name = "HUNT_THE_WUMPUS";
$password = "";

$no_of_rows = 5;
$no_of_cols = 5;
$no_of_initial_wumpuses = 5;

function randomWumpuseLocation($row_num_max, $col_num_max, $skips) {
  $rand_row_num = mt_rand(0, $row_num_max - 1);
  $rand_col_num = mt_rand(0, $col_num_max - 1);

  foreach ($skips as $skip) {
      if ($rand_row_num === $skip['row_num'] && $rand_col_num === $skip['col_num']) {
          $rand_col_num = mt_rand(0, $col_num_max - 1);
      }
  }
  return array("row_num"=>$rand_row_num, "col_num"=>$rand_col_num);
}

// Create connection
try
{
    $conn = new PDO("mysql:host=$servername;dbname=$db_name", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // begin the transaction
    $conn->beginTransaction();

    $init_check_wumpuse = $conn->prepare("SELECT 'YES' INITIALIZED FROM WUMPUSES WHERE ROW_NUM = :row_num AND COL_NUM = :col_num;");
    $init_check_wumpuse->bindParam(':row_num', $rand_row_num);
    $init_check_wumpuse->bindParam(':col_num', $rand_col_num);

    $rand_row_num = 2;
    $rand_col_num = 2;
    $init_check_wumpuse->execute();

    // Initial script
    if($init_check_wumpuse->rowCount() == 0) {

        $init_insert_wumpuse = $conn->prepare("INSERT INTO WUMPUSES(ROW_NUM, COL_NUM) VALUES(:row_num, :col_num);");
        $init_insert_wumpuse->bindParam(':row_num', $rand_row_num);
        $init_insert_wumpuse->bindParam(':col_num', $rand_col_num);

        for ($i = 0;$i < $no_of_initial_wumpuses; $i++) {
            $init_insert_wumpuse->execute();

            $randomWumpuse = randomWumpuseLocation($no_of_rows, $no_of_cols, array(array("row_num"=>0, "col_num"=>0)));

            $rand_row_num = $randomWumpuse["row_num"];
            $rand_col_num = $randomWumpuse["col_num"];
        }
    }

    // commit the transaction
    $conn->commit();
} catch(PDOException $e) {
    // roll back the transaction if something failed
    $conn->rollback();

    echo "Error occurred: " . $e->getMessage();
}



?>
