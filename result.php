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

            try{
                $row_num = $_GET['row'];
                $col_num = $_GET['col'];

                // begin the transaction
                $conn->beginTransaction();

                $delete_wumpuse = $conn->prepare("DELETE FROM WUMPUSES WHERE ROW_NUM = :row_num AND COL_NUM = :col_num;");
                $delete_wumpuse->bindParam(':row_num', $row_num);
                $delete_wumpuse->bindParam(':col_num', $col_num);
                $delete_wumpuse->execute();

                $status = 'lose';

                if ($delete_wumpuse->rowCount() > 0) {
                    $status = 'win';
                    $randomWumpuse = array("row_num"=>2, "col_num"=>2);
                    if($row_num != 2 && $col_num != 2) {
                        $randomWumpuse = randomWumpuseLocation($no_of_rows, $no_of_cols, array(array("row_num"=>0, "col_num"=>0), array("row_num"=>2, "col_num"=>2)));
                    }
                    
                    $insert_wumpuse = $conn->prepare("INSERT INTO WUMPUSES(ROW_NUM, COL_NUM) VALUES(:row_num, :col_num);");
                    $insert_wumpuse->bindParam(':row_num', $randomWumpuse["row_num"]);
                    $insert_wumpuse->bindParam(':col_num', $randomWumpuse["col_num"]);
                    $insert_wumpuse->execute();
                }
                // commit the transaction
                $conn->commit();
                $conn = null;
            } catch(PDOException $e) {
                // roll back the transaction if something failed
                $conn->rollback();

                echo "Error occurred: " . $e->getMessage();
            }

        ?>
        <h2 class='result <?php echo $status?>'><i></i><p></p></h2>
        <div>
            <form action="save.php" method="POST">
                <fieldset>
                    <legend>Save your score</legend>
                    <input type="email" name="email" placeholder="email@example.com" required area-required="true">
                    <input type="hidden" id="status" name="status" value="<?php echo $status?>">
                    <input type="submit" value="Save">
                </fieldset>
            </form>
        </div>
    </div>
</body>

</html>