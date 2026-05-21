<?php
/**
 * delete_claim.php
 * Endpoint AJAX — suppression d'une réclamation (admin uniquement)
 * Placer à la racine du projet, au même niveau que trial.php
 */

session_start();
include("dbase.php");

header('Content-Type: application/json; charset=utf-8');

// Vérification de session et de rôle
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Accès refusé']);
    exit;
}

// Vérification de la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

// Validation de l'ID
$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID invalide']);
    exit;
}

// Vérifier que la réclamation existe
$stmt = $conn->prepare("SELECT id FROM claim WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    echo json_encode(['success' => false, 'message' => 'Réclamation introuvable']);
    exit;
}
$stmt->close();

// Supprimer d'abord les affectations liées (contrainte de clé étrangère)
$stmt = $conn->prepare("DELETE FROM affect_rec WHERE id_claim = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Supprimer la réclamation
$stmt = $conn->prepare("DELETE FROM claim WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$deleted = $stmt->affected_rows;
$stmt->close();

if ($deleted > 0) {
    echo json_encode(['success' => true, 'message' => 'Réclamation supprimée avec succès']);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
}
?>