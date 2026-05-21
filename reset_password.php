<?php
	session_start();
	if(!isset($_SESSION['login']))
	{
		header('Location: try_log.php');
		die;
	}
	include("dbase.php");
	
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[random_int(0, $charactersLength - 1)];
		}
		return $randomString;
	}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Réinitialisation du mot de passe</title>
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="shortcut icon" href="images/loggo.png" />
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    .page-wrapper {
      background: #ffffff;
      min-height: 500px;
      padding: 36px 28px;
      font-family: 'Cairo', sans-serif;
      border-radius: 12px;
      margin: 20px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .page-title {
      text-align: center;
      color: #1aa957;
      font-size: 24px;
      font-weight: 700;
      letter-spacing: 1px;
      margin-bottom: 30px;
    }
    .table-container {
      border-radius: 10px;
      overflow: hidden;
      border: 1px solid #e5e7eb;
    }
    .reset-table { width: 100%; border-collapse: collapse; }
    .reset-table thead tr { background: #1aa957; }
    .reset-table th {
      padding: 15px 22px;
      font-size: 14px;
      font-weight: 700;
      color: #fff;
      text-align: center;
      letter-spacing: 0.6px;
      font-family: 'Cairo', sans-serif;
    }
    .reset-table tbody tr {
      background: #fff;
      border-bottom: 1px solid #f0f0f0;
      transition: background 0.15s;
    }
    .reset-table tbody tr:hover { background: #f0fdf4; }
    .reset-table td {
      padding: 18px 22px;
      color: #111827;
      font-size: 14px;
      font-family: 'Cairo', sans-serif;
      vertical-align: middle;
      text-align: center;
    }
    .name-cell {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    .avatar {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 36px; height: 36px;
      border-radius: 50%;
      font-weight: 700;
      font-size: 13px;
      flex-shrink: 0;
      background: #d1fae522;
      color: #1aa957;
      border: 1.5px solid #1aa95766;
    }
    .dept-badge {
      display: inline-block;
      padding: 4px 14px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      background: #eff6ff;
      color: #2563eb;
      border: 1px solid #bfdbfe;
      font-family: 'Cairo', sans-serif;
    }
    .date-cell {
      font-size: 13px;
      color: #6b7280;
      background: #f9fafb;
      padding: 5px 12px;
      border-radius: 6px;
      border: 1px solid #e5e7eb;
      display: inline-block;
      font-family: monospace;
    }
    .btn-reinit {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 22px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      font-family: 'Cairo', sans-serif;
      cursor: pointer;
      transition: all 0.2s;
      background: #1aa957;
      color: #fff;
      border: none;
      box-shadow: 0 2px 8px rgba(26,169,87,0.25);
    }
    .btn-reinit:hover {
      background: #17a050;
      box-shadow: 0 4px 14px rgba(26,169,87,0.35);
      transform: translateY(-1px);
    }
    .btn-reinit:active { transform: translateY(0); }

    .new-password-box {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      background: #f0fdf4;
      border: 1.5px solid #6ee7b7;
      border-radius: 10px;
      padding: 10px 18px;
    }
    .new-password-value {
      font-size: 22px;
      font-weight: 700;
      color: #065f46;
      font-family: monospace;
      letter-spacing: 3px;
    }
    .copy-btn {
      background: none;
      border: 1px solid #6ee7b7;
      border-radius: 6px;
      padding: 4px 10px;
      color: #1aa957;
      cursor: pointer;
      font-size: 12px;
      font-family: 'Cairo', sans-serif;
      transition: all 0.2s;
    }
    .copy-btn:hover { background: #1aa957; color: #fff; }

    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #9ca3af;
    }
    .empty-state .icon {
      font-size: 48px;
      margin-bottom: 12px;
    }
    .empty-state p {
      font-size: 15px;
      font-family: 'Cairo', sans-serif;
    }
    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 18px;
      border-radius: 8px;
      font-size: 14px;
      font-family: 'Cairo', sans-serif;
      font-weight: 600;
      text-decoration: none;
      background: #f3f4f6;
      color: #374151;
      border: 1px solid #d1d5db;
      transition: all 0.2s;
      margin-bottom: 20px;
    }
    .back-btn:hover { background: #e5e7eb; color: #111; }

    /* Toast notification */
    .toast {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background: #065f46;
      color: #fff;
      padding: 12px 22px;
      border-radius: 8px;
      font-family: 'Cairo', sans-serif;
      font-size: 14px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.3s;
      z-index: 9999;
      pointer-events: none;
    }
    .toast.show { opacity: 1; transform: translateY(0); }
  </style>
</head>

<body>

<!-- NAVBAR -->
<div class="horizontal-menu">
  <nav class="navbar top-navbar col-lg-12 col-12 p-0">
    <div class="container-fluid">
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-between">
        <ul class="navbar-nav navbar-nav-left">
          <li class="nav-item dropdown d-lg-flex d-none">
            <a class="dropdown-toggle show-dropdown-arrow btn btn-inverse-primary btn-sm" id="nreportDropdown" href="#" data-bs-toggle="dropdown" style="background-color:#23894e; color:white">
              Reports
            </a>
            <div class="dropdown-menu dropdown-menu-left navbar-dropdown preview-list" aria-labelledby="nreportDropdown">
              <p class="mb-0 font-weight-medium float-left dropdown-header">Reports</p>
              <a class="dropdown-item"><i class="mdi mdi-file-pdf text-primary"></i> Pdf</a>
              <a class="dropdown-item"><i class="mdi mdi-file-excel text-primary"></i> Excel</a>
            </div>
          </li>
        </ul>
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
          <a class="navbar-brand brand-logo">
            <img src="http://localhost:8888/reclamation/images/auth/loggo.png" alt="logo" style="width:200px; height:78px">
          </a>
          <a class="navbar-brand brand-logo-mini" href="index.html">
            <img src="http://localhost:8888/reclamation/images/auth/loggo.png" alt="logo" style="width:200px; height:78px">
          </a>
        </div>
        <ul class="navbar-nav navbar-nav-right">
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown">
              <span class="nav-profile-name"><?php echo $_SESSION['full_name']; ?></span>
              <span class="online-status"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown" data-bs-popper="none">
              <a class="dropdown-item" href="deconnexion.php">
                <i class="mdi mdi-logout text-primary" style="color:#1aa957 !important"></i>
                Deconnexion
              </a>
            </div>
          </li>
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="horizontal-menu-toggle">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </div>
  </nav>
  <nav class="bottom-navbar">
    <div class="container">
      <ul class="nav page-navigation">
        <li class="nav-item active">
          <a class="nav-link" href="trial.php">
            <i class="mdi mdi-file-document-box menu-icon"></i>
            <span class="menu-title">Accueil</span>
          </a>
        </li>
        <?php if($_SESSION['role'] == 'employee') { ?>
        <li class="nav-item">
          <a href="trying.php" class="nav-link">
            <i class="mdi mdi-content-paste menu-icon"></i>
            <span class="menu-title">Formulaire</span>
            <i class="menu-arrow"></i>
          </a>
        </li>
        <?php } ?>
        <li class="nav-item">
          <a href="teams.php" class="nav-link">
            <i class="mdi mdi-wrench menu-icon"></i>
            <span class="menu-title">Equipe de maintenance</span>
            <i class="menu-arrow"></i>
          </a>
        </li>
        <li class="nav-item">
          <a href="tab_employee.php" class="nav-link">
            <i class="mdi mdi-human-greeting menu-icon"></i>
            <span class="menu-title">Employés</span>
            <i class="menu-arrow"></i>
          </a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="mdi mdi-codepen menu-icon"></i>
            <span class="menu-title">Autres pages</span>
            <i class="menu-arrow"></i>
          </a>
          <div class="submenu">
            <ul class="submenu-item">
              <li class="nav-item"><a class="nav-link" href="http://localhost:8888/reclamation/register-2.php">Register</a></li>
              <li class="nav-item"><a class="nav-link" href="http://localhost:8888/reclamation/login.php">Login</a></li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</div>

<!-- CONTENU PRINCIPAL -->
<div class="page-wrapper">

  <a class="back-btn" href="tab_employee.php">
    ← Retour aux employés
  </a>

  <h2 class="page-title">🔑 Réinitialisation du mot de passe</h2>

  <?php
    $id_emp = null;
    $newCode = null;

    if($_SERVER["REQUEST_METHOD"] == "POST") {
      $id_emp = filter_input(INPUT_POST, "id_emp", FILTER_SANITIZE_SPECIAL_CHARS);
    }
    if(isset($_GET['id_emp']) && !empty($_GET['id_emp'])) {
      $id_emp = $_GET['id_emp'];
    }

    if($id_emp) {
      $sql = "SELECT U.full_name, U.id as id_user, U.mdp, U.departement, D.name 
              FROM users U, departments D 
              WHERE U.departement = D.id AND U.id = $id_emp";
      $result = mysqli_query($conn, $sql);
      $emp = mysqli_fetch_assoc($result);

      // Generate new code
      $newCode = generateRandomString(4);
      $sqlUpdate = "UPDATE users SET code = '$newCode' WHERE id = $id_emp";
      mysqli_query($conn, $sqlUpdate);

      function getInitials($name) {
        $parts = explode(' ', trim($name));
        $ini = '';
        foreach($parts as $p) $ini .= strtoupper($p[0] ?? '');
        return substr($ini, 0, 2);
      }
    }
  ?>

  <?php if($id_emp && $emp): ?>
  <div class="table-container">
    <table class="reset-table">
      <thead>
        <tr>
          <th>Nom complet</th>
          <th>Service</th>
          <th>Date de demande</th>
          <th>Action</th>
          <th>Nouveau mot de passe</th>
        </tr>
      </thead>
      <tbody>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <input type="hidden" name="id_emp" value="<?php echo $id_emp; ?>">
          <tr>
            <td>
              <div class="name-cell">
                <div class="avatar"><?php echo getInitials($emp['full_name']); ?></div>
                <?php echo htmlspecialchars($emp['full_name']); ?>
              </div>
            </td>
            <td>
              <span class="dept-badge"><?php echo htmlspecialchars($emp['name']); ?></span>
            </td>
            <td>
              <span class="date-cell">
                <?php echo htmlspecialchars($emp['mdp'] ?: '—'); ?>
              </span>
            </td>
            <td>
              <button type="submit" name="submit" class="btn-reinit">
                🔄 Réinitialiser
              </button>
            </td>
            <td>
              <div class="new-password-box">
                <span class="new-password-value" id="newPwd"><?php echo htmlspecialchars($newCode); ?></span>
                <button type="button" class="copy-btn" onclick="copyPwd()">📋 Copier</button>
              </div>
            </td>
          </tr>
        </form>
      </tbody>
    </table>
  </div>

  <?php else: ?>
  <div class="empty-state">
    <div class="icon">🔒</div>
    <p>Aucun employé sélectionné.<br>Veuillez revenir à la liste des employés.</p>
    <br>
    <a href="tab_employee.php" class="back-btn">← Retour aux employés</a>
  </div>
  <?php endif; ?>

</div>

<!-- Toast -->
<div class="toast" id="toast">✅ Mot de passe copié !</div>

<script src="vendors/base/vendor.bundle.base.js"></script>
<script src="js/template.js"></script>

<script>
  function copyPwd() {
    const pwd = document.getElementById('newPwd').textContent;
    navigator.clipboard.writeText(pwd).then(() => {
      const toast = document.getElementById('toast');
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 2500);
    });
  }
</script>

</body>
</html>