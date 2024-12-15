<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);  
ini_set('display_errors', 0);  

require 'db.php';

require 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
// Récupérer les scores des autres joueurs
$stmt = $pdo->prepare("SELECT username, score FROM users ORDER BY score DESC");
$stmt->execute();
$scores = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (!$user) {
    header("Location: logout.php");
    exit;
}


if (!isset($_SESSION['target'])) {
    $_SESSION['target'] = rand(1, 100);
}

if (!isset($_SESSION['attempts']) || $_SESSION['attempts'] <= 0) {
    $_SESSION['attempts'] = 10; 
}


if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = $user['score']; 
}

$message = "";
$game_over = false;
$hint = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guess = (int)$_POST['guess'];
    $_SESSION['attempts']--; 

    if ($guess === $_SESSION['target']) {
        $_SESSION['score'] += $_SESSION['attempts']; 
        $stmt = $pdo->prepare("UPDATE users SET score = ? WHERE id = ?");
        $stmt->execute([$_SESSION['score'], $_SESSION['user_id']]);

        $message = "Félicitations ! Vous avez deviné le nombre !";
        $game_over = true;
        unset($_SESSION['target']);
        unset($_SESSION['attempts']);
    } elseif ($_SESSION['attempts'] <= 0) {
        $message = "Vous avez perdu ! Le nombre était {$_SESSION['target']}.";
        $game_over = true;
        unset($_SESSION['target']);
        unset($_SESSION['attempts']);
    } else {
        $hint = $guess < $_SESSION['target'] ? "Trop petit !" : "Trop grand !";
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu - Deviner le Nombre</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            color: #fff;
            margin: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0.8);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        .header h1 {
            margin: 0;
            font-size: 1.5em;
        }

        .header .logout-btn {
            padding: 10px 15px;
            background-color: #ff5252;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .header .logout-btn:hover {
            background-color: #e63939;
        }

        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .game {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        .game h2 {
            margin-bottom: 20px;
        }

        .game form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .game input[type="number"] {
            padding: 10px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
        }

        .game button {
            padding: 10px 20px;
            font-size: 1em;
            background-color: #00c9a7;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 50px;
        }

        .game button:hover {
            background-color: #00a383;
        }

        .game .info {
            margin-top: 20px;
        }
        
    </style>
</head>
<body>
    
    <div class="main-content">
        <div class="game">
            <h2>Devinez le Nombre</h2>
            <p>Tentatives restantes : <?= $_SESSION['attempts'] ?></p>
            <p>Score actuel : <?= $_SESSION['score'] ?></p>
            
            

            <?php if ($game_over): ?>
                <p class="info"><?= htmlspecialchars($message) ?></p>
                <form method="POST" action="game.php">
                    <button type="submit" name="replay">Rejouer</button>
                </form>
            <?php else: ?>
                <form method="POST">
                    <input type="number" name="guess" placeholder="Votre proposition" required>
                    <button type="submit">Deviner</button>
                </form>
                <?php if ($hint): ?>
                    <p class="info"><?= htmlspecialchars($hint) ?></p>
                <?php endif; ?>
                
            <?php endif; ?>
            <div style="margin-top: 20px;">
    <button onclick="window.location.href='leaderboard.php'" 
            style="padding: 10px 20px; 
                   background-color: #d3d3d3; 
                   color: #333; 
                   border: none; 
                   border-radius: 5px; 
                   cursor: pointer;">
        Voir le classement
    </button>
</div>

        </div>
    </div>

    <?php
    if (isset($_POST['replay'])) {
        $_SESSION['target'] = rand(1, 100);  
        $_SESSION['attempts'] = 10;  
        $_SESSION['score'] = $user['score'];  
        header("Location: game.php");  
        exit;
    }
    ?>
</body>
</html>
