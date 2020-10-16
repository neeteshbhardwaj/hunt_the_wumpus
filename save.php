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
                $email = $_REQUEST["email"];
                $status = $_REQUEST["status"];
                $last_played_date = date('Y-m-d');
                $wins = ($status === 'win') ? 1 : 0;
                $loses = ($status === 'lose') ? 1 : 0;

                $find_player = "SELECT WINS, LOSES FROM PLAYERS WHERE EMAIL = '$email'";
                $find_player_result = mysqli_query($conn, $find_player); 

                if(mysqli_affected_rows($conn) === 1) {
                    $row = mysqli_fetch_assoc($find_player_result);
                    $wins += $row["WINS"];
                    $loses += $row["LOSES"];
                    
                    $update_player = "UPDATE PLAYERS SET WINS = $wins, LOSES = $loses, LAST_PLAYED_DATE = '$last_played_date' WHERE EMAIL = '$email';";
                    $update_player_result = mysqli_query($conn, $update_player); 

                    if(mysqli_affected_rows($conn) != 1) {
                        echo "Error occurred: " . mysqli_connect_error();
                    }

                } else {
                    $insert_player = "INSERT INTO PLAYERS(EMAIL, WINS, LOSES, LAST_PLAYED_DATE) VALUES('$email', $wins, $loses, '$last_played_date');";
                    $insert_player_result = mysqli_query($conn, $insert_player); 

                    if(mysqli_affected_rows($conn) != 1) {
                        echo "Error occurred: " . mysqli_connect_error();
                    }
                }

                echo "<div class='card info'>";
                echo "<h3>Your game has been recorded</h3>";
                echo "<p>You have $wins <span>wins</span>, $loses <span>loses</span> till $last_played_date</p>";
                echo "</div>";

                
                echo "<div class='page-header'><p>Leaderboard</p><a href='index.php'>Play again</a></div>";
                
                $top_ten_players = "SELECT EMAIL, WINS, LOSES, LAST_PLAYED_DATE FROM PLAYERS ORDER BY WINS DESC LIMIT 10;";
                $top_ten_players_result = mysqli_query($conn, $top_ten_players); 

                if (mysqli_affected_rows($conn) > 0) {
                    // output data of each row
                    while($player = mysqli_fetch_assoc($top_ten_players_result)) {
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

                $conn->close();

            ?>
        </div>
        <div class="page-footer"><a href="index.php">Play again</a></div>
    </div>
</body>

</html>