<?php
require 'db.php';
require 'header.php';

$stmt = $pdo->query("SELECT username, score FROM users ORDER BY score DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classement</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #4facfe, #00f2fe);
            color: #fff;
            height: 100vh;
            display: flex;
            flex-direction: column; 
            justify-content: center;
            align-items: center;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0.8);
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            width: 100%; 
            position: absolute; 
            top: 0;
            left: 0;
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

        
        .content-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 100px; 
        }

        
        .container {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 90%;
            max-width: 600px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        }

        h2 {
            margin-bottom: 20px;
        }

        ol {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: rgba(255, 255, 255, 0.1);
        }

        li:first-child {
            font-size: 1.2em;
            font-weight: bold;
            background-color: #ffdd57;
            color: #000;
        }

        li:nth-child(2) {
            background-color: #c0c0c0;
            color: #000;
        }

        li:nth-child(3) {
            background-color: #cd7f32;
            color: #000;
        }

        img.trophy {
            width: 30px;
            height: auto;
            margin-right: 10px;
        }

        .username {
            flex-grow: 1;
            text-align: left;
        }

        .score {
            font-weight: bold;
        }

        

        button {
            padding: 10px 20px;
            background-color: #d3d3d3;
            color: #333;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 200px; 
        }

        button:hover {
            background-color: #bbb;
        }
        .gap{
            height:10px
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h2>Classement des utilisateurs</h2>
        <?php if (count($users) > 0): ?>
            <ol>
                <?php foreach ($users as $index => $user): ?>
                    <li>
                        <?php if ($index === 0): ?>
                            <img class="trophy" src="assets/gold_trophy.png" alt="Trophée Or">
                        <?php elseif ($index === 1): ?>
                            <img class="trophy" src="assets/silver_trophy.png" alt="Trophée Argent">
                        <?php elseif ($index === 2): ?>
                            <img class="trophy" src="assets/bronze_trophy.png" alt="Trophée Bronze">
                        <?php endif; ?>
                        <span class="username"><?= htmlspecialchars($user['username']) ?></span>
                        <span class="score"><?= htmlspecialchars($user['score']) ?> points</span>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <p>Aucun utilisateur trouvé.</p>
        <?php endif; ?>

        <div >
            <button onclick="window.location.href='game.php'">Jouer au jeu</button>
        </div>
        <div class="gap"></div>
        <div>
                <form action="message.php" method="get">
                <button type="submit">Accéder au Chat</button>
            </form>
        </div>
           
        
    </div>
</body>
</html>
