<!DOCTYPE html>
<html>

<head>
    <title>Hunt the Wumpus! - Result</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/wumpus.css">
</head>

<body>
    <div id="container">
        <h1>Hunt the Wumpus!</h1>
        <?php 
            require 'ensureDBConnection.php';

            $row_num = $_GET['row'];
            $col_num = $_GET['col'];

            $delete_wumpuse = "DELETE FROM WUMPUSES WHERE ROW_NUM = $row_num AND COL_NUM = $col_num;";
            $delete_wumpuse_result = mysqli_query($conn, $delete_wumpuse); 

            $status = 'lose';

            if (mysqli_affected_rows($conn) > 0) {
                $status = 'win';

                $rand_row_num = mt_rand(0, $no_of_rows - 1);
                $rand_col_num = mt_rand(0, $no_of_cols - 1);

                while( ($rand_row_num === 0 && $rand_col_num === 0) || ($rand_row_num === 2 && $rand_col_num === 2) ) {
                    $rand_col_num = mt_rand(0, $no_of_cols - 1);
                }

                $insert_wumpuse = "INSERT INTO WUMPUSES(ROW_NUM, COL_NUM) VALUES($rand_row_num, $rand_col_num);";
                $insert_wumpuse_result = mysqli_query($conn, $insert_wumpuse);
                
                if(mysqli_affected_rows($conn) != 1) {
                    echo "Error occurred: " . mysqli_connect_error();
                }
            }
            echo "<script>const status = '$status'; </script>";
            $conn->close();

        ?>
        <h2 id='result'><i></i><p></p></h2>
        <div>
            <form action="save.php" method="POST">
                <fieldset>
                    <legend>Save your score</legend>
                    <input type="email" name="email" placeholder="email@example.com" required area-required="true">
                    <input type="hidden" id="status" name="status" value="">
                    <input type="submit" value="Save">
                </fieldset>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('status').value = status;
        document.getElementById('result').setAttribute("class", "result " + status);
    </script>
</body>

</html>