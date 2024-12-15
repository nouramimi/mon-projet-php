<?php
session_start();
require 'db.php';

$stmt = $pdo->prepare("SELECT * FROM games WHERE game_id = ?");
$stmt->execute([$_SESSION['game_id']]);
$game = $stmt->fetch();

$response = [];
if ($game) {
    if ($game['attempts'] <= 0 || $game['player1_score'] >= 100 || $game['player2_score'] >= 100) {
        $response['status'] = 'game_over';
        $winner = $game['player1_score'] >= 100 ? 'Player 1' : 'Player 2';
        $response['message'] = "Game over! The winner is: $winner";
    } else {
        $response['status'] = 'ongoing';
    }
} else {
    $response['status'] = 'error';
}

echo json_encode($response);
?>
