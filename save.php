<!DOCTYPE html>
<html>

<head>
    <title>Hunt the Wumpus! - Leaderboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/wumpus.css">
</head>

<body>
    <div id="container">
        <h1>Hunt the Wumpus!</h1>
        <div>
        <?php 
                require 'ensureDBConnection.php';

                try {
                    // begin the transaction
                    $conn->beginTransaction();

                    $email = $_REQUEST["email"];
                    $status = $_REQUEST["status"];
                    $last_played_date = date('Y-m-d');
                    $wins = ($status === 'win') ? 1 : 0;
                    $loses = ($status === 'lose') ? 1 : 0;
                    $sql = "INSERT INTO PLAYERS(EMAIL, WINS, LOSES, LAST_PLAYED_DATE) VALUES(:email, :wins, :loses, :last_played_date);";

                    $find_player = $conn->prepare("SELECT WINS, LOSES FROM PLAYERS WHERE EMAIL = :email");
                    $find_player->bindParam(':email', $email);
                    $find_player->execute(); 
                    
                    if($find_player->rowCount() === 1) {
                        $find_player_result = $find_player->fetch(PDO::FETCH_ASSOC);
                        $wins += $find_player_result["WINS"];
                        $loses += $find_player_result["LOSES"];
                        $sql = "UPDATE PLAYERS SET WINS = :wins, LOSES = :loses, LAST_PLAYED_DATE = :last_played_date WHERE EMAIL = :email;";
                    } 
                    $upsert_player = $conn->prepare($sql); 
                    $upsert_player->bindParam(':wins', $wins);
                    $upsert_player->bindParam(':loses', $loses);
                    $upsert_player->bindParam(':last_played_date', $last_played_date);
                    $upsert_player->bindParam(':email', $email);
                    $upsert_player->execute(); 

                    // commit the transaction
                    $conn->commit();
                } catch(PDOException $e) {
                    // roll back the transaction if something failed
                    $conn->rollback();

                    echo "Error occurred: " . $e->getMessage();
                }
            ?>
            <div class="card info">
                <h3>Your game has been recorded</h3>
                <p>You have <?php echo $wins ?> <span>wins</span>, <?php echo $loses ?> <span>loses</span> till <?php echo $last_played_date ?></p>
            </div>
            <div class="page-header"><p>Leaderboard</p><a href="index.php">Play again</a></div>
            <?php 
                try {
                    $top_ten_players = $conn->prepare("SELECT EMAIL, WINS, LOSES, LAST_PLAYED_DATE FROM PLAYERS ORDER BY WINS DESC LIMIT 10;");
                    $top_ten_players->execute();
                    $top_ten_players_result = $top_ten_players->fetchAll(PDO::FETCH_ASSOC);

                    if ($top_ten_players->rowCount() > 0) {
                        // output data of each row
                        foreach($top_ten_players_result as $player) {
                            $e = $player["EMAIL"];
                            $w = $player["WINS"];
                            $l = $player["LOSES"];
                            $d = $player["LAST_PLAYED_DATE"];
                            echo "<div class='card'>";
                            echo "<h3><span>Email:</span> $e</h3>";
                            echo "<p><span>Wins:</span> $w</p>";
                            echo "<p><span>Loses:</span> $l</p>";
                            echo "<p><span>Last Played on:</span> $d</p>";
                            echo "</div>";
                        }
                    }
                    $conn = null;
                } catch(PDOException $e) {
                    echo "Error occurred: " . $e->getMessage();
                }

            ?>
        </div>
        <div class="page-footer"><a href="index.php">Play again</a></div>
    </div>
</body>

</html>