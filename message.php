<?php
session_start();
require 'db.php';
require 'header.php';

if (!isset($_SESSION['user_id'])) {
    die("Accès refusé");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $message = htmlspecialchars($_POST['message']);
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
    $stmt->execute([$user_id, $message]);
    exit;
}


$stmt = $pdo->query("SELECT messages.message, users.username, messages.created_at 
                     FROM messages
                     JOIN users ON messages.user_id = users.id
                     ORDER BY messages.created_at ASC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);


if ($messages === false) {
    $messages = [];
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        /* En-tête */
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
            color: white;
        }

        .header .logout-btn {
            padding: 10px 15px;
            background-color: #ff5252;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .header .logout-btn:hover {
            background-color: #e63939;
        }

        /* Conteneur de chat */
        .chat-container {
            max-width: 800px;
            margin: 20px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        /* Section des messages */
        .messages {
            height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fafafa;
            border-radius: 5px;
        }

        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f0f0f0;
        }

        .message .username {
            font-weight: bold;
        }

        .message .time {
            font-size: 0.8em;
            color: #888;
        }

        /* Zone de saisie du message */
        .input-area {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .input-area input {
            width: 80%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .input-area button {
            width: 15%;
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            font-size: 1em;
        }

        /* Bouton de retour */
        .back-button {
            margin-top: 20px;
            text-align: center;
        }

        .back-button a {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .back-button a:hover {
            background-color: #0056b3;
        }

        /* Responsivité */
        @media (max-width: 768px) {
            .input-area {
                flex-direction: column;
            }

            .input-area input,
            .input-area button {
                width: 100%;
            }
        }

    </style>
</head>
<body>
    <div class="chat-container">
        <div class="messages" id="messages">
            <?php if (count($messages) > 0): ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message">
                        <span class="username"><?= htmlspecialchars($msg['username']) ?></span> 
                        <span class="time"><?= htmlspecialchars($msg['created_at']) ?></span>
                        <p><?= htmlspecialchars($msg['message']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun message disponible.</p>
            <?php endif; ?>
        </div>

        <div class="input-area">
            <input type="text" id="message" placeholder="Votre message...">
            <button id="send-message">Envoyer</button>
        </div>
    </div>

    <div class="back-button">
        <a href="leaderboard.php">Retour au Leaderboard</a>
    </div>

    <script>
        // Fonction pour envoyer un message
        $('#send-message').click(function() {
            var message = $('#message').val();
            if (message) {
                $.post('message.php', { message: message }, function() {
                    $('#message').val('');  // Réinitialiser l'input
                    loadMessages();  // Recharger les messages
                });
            }
        });

        // Fonction pour charger les messages
        function loadMessages() {
            $.get('message.php', function(data) {
                var messages = $(data).find('.messages').html();
                $('#messages').html(messages);
                $('#messages').scrollTop($('#messages')[0].scrollHeight);  // Faire défiler vers le bas
            });
        }

        // Rafraîchir les messages toutes les 2 secondes
        setInterval(loadMessages, 2000);
    </script>
</body>
</html>
