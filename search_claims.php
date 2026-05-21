<?php
session_start();
header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

include("dbase.php");

$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';
$status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';

// Construire la requête SQL
$sql = "SELECT C.id, C.status, C.place, C.damage, C.description, C.date_add, C.id_type, C.user_id, U.full_name, U.departement, U.id, T.type FROM claim C, users U, type_reclamation T WHERE C.user_id = U.id AND C.id_type = T.id";

// Appliquer les filtres
if (!empty($search)) {
    $sql .= " AND (U.full_name LIKE '%$search%' OR C.place LIKE '%$search%' OR C.description LIKE '%$search%' OR T.type LIKE '%$search%')";
}

if (!empty($status)) {
    $status_map = [
        'pending' => 'pending',
        'in_progress' => 'In progress',
        'completed' => 'Completed'
    ];
    
    if (isset($status_map[$status])) {
        $sql .= " AND C.status = '{$status_map[$status]}'";
    }
}

// Gérer les rôles
if ($_SESSION['role'] === 'team') {
    // Les équipes ne voient que leurs réclamations assignées
    $sql = "SELECT C.id, C.status, C.place, C.damage, C.description, C.date_add, C.id_type, C.user_id, U.full_name, U.departement, U.id, T.type FROM claim C, users U, type_reclamation T, affect_rec A WHERE C.user_id = U.id AND C.id_type = T.id AND A.id_claim = C.id AND A.id_employee = " . $_SESSION['user_id'];
    
    if (!empty($search)) {
        $sql .= " AND (U.full_name LIKE '%$search%' OR C.place LIKE '%$search%' OR C.description LIKE '%$search%' OR T.type LIKE '%$search%')";
    }
    
    if (!empty($status)) {
        $status_map = [
            'pending' => 'pending',
            'in_progress' => 'In progress',
            'completed' => 'Completed'
        ];
        
        if (isset($status_map[$status])) {
            $sql .= " AND C.status = '{$status_map[$status]}'";
        }
    }
} elseif ($_SESSION['role'] === 'employee') {
    // Les employés ne voient que leurs propres réclamations
    $sql .= " AND C.user_id = " . $_SESSION['user_id'];
}

$sql .= " ORDER BY C.date_add DESC";

$result = mysqli_query($conn, $sql);

if (!$result) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la recherche: ' . mysqli_error($conn)
    ]);
    exit;
}

// Récupérer les affectations pour construire les infos d'équipe
$affect_sql = "SELECT id_claim, id_employee, full_name FROM affect_rec A JOIN users U ON A.id_employee = U.id";
$affect_result = mysqli_query($conn, $affect_sql);
$emp_affect = [];

while ($row = mysqli_fetch_assoc($affect_result)) {
    if (!isset($emp_affect[$row['id_claim']])) {
        $emp_affect[$row['id_claim']] = [];
    }
    $emp_affect[$row['id_claim']][] = $row['full_name'];
}

// Construire les lignes du tableau
$rows = [];
while ($row = mysqli_fetch_assoc($result)) {
    $team_info = '';
    $modify_btn = '';
    $show_add_btn = false;
    $show_modify_btn = false;
    $show_delete_btn = false;
    
    if ($_SESSION['role'] === 'responsable') {
        if (!isset($emp_affect[$row['id']])) {
            $show_add_btn = true;
            $team_info = '';
        } else {
            $show_modify_btn = true;
            $team_info = implode(', ', $emp_affect[$row['id']]);
        }
    }
    
    // Afficher le bouton de suppression pour les admins uniquement
    if ($_SESSION['role'] === 'admin') {
        $show_delete_btn = true;
    }
    
    $rows[] = [
        'id' => $row['id'],
        'full_name' => $row['full_name'],
        'damage' => $row['damage'],
        'status' => $row['status'],
        'date_add' => $row['date_add'],
        'type' => $row['type'],
        'team_info' => $team_info,
        'showTeamColumn' => ($_SESSION['role'] === 'responsable'),
        'showAddBtn' => $show_add_btn,
        'showModifyBtn' => $show_modify_btn,
        'showDeleteBtn' => $show_delete_btn
    ];
}

echo json_encode([
    'success' => true,
    'rows' => $rows,
    'count' => count($rows)
]);
?>
