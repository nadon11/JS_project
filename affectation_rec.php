<?php
	session_start();
	if(!isset($_SESSION['login']))
	{
		header('Location: login.php');
		die;
	}
	include("dbase.php");

	$error = '';
	$right = '';

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$id_rec    = intval(filter_input(INPUT_POST, "id_rec", FILTER_SANITIZE_SPECIAL_CHARS));
		$employees = isset($_POST["employees"]) ? $_POST["employees"] : array();

		if(count($employees) < 1) {
			$error = "Aucun employé n'a été sélectionné";
		} else {
			$sql    = "SELECT C.*, U.full_name, U.departement, T.type FROM claim C, users U, type_reclamation T WHERE C.user_id=U.id AND C.id_type=T.id AND C.id=$id_rec";
			$result = mysqli_query($conn, $sql);
			if($row = mysqli_fetch_assoc($result)){
				mysqli_query($conn, "DELETE FROM affect_rec WHERE id_claim=$id_rec");
				$nb_ajout = 0;
				foreach($employees as $emp) {
					$emp = intval($emp);
					if(mysqli_query($conn, "INSERT INTO affect_rec(id_claim,id_employee) VALUES ($id_rec,$emp)"))
						$nb_ajout++;
					else
						$error = "Une erreur est survenue lors de la saisie des données";
				}
				if($nb_ajout == count($employees)) {
					mysqli_query($conn, "UPDATE claim SET status='In progress' WHERE id=$id_rec");

					$_SESSION['flash_success'] = "Équipe affectée avec succès — statut mis à jour";
					header("Location: affectation_rec.php?id_rec=$id_rec");
					exit;
				} else {
					$error = "Une erreur est survenue lors de la saisie des données";
				}
			} else {
				$error = "Réclamation introuvable";
			}
		}
	}

	if(isset($_SESSION['flash_success'])) {
		$right = $_SESSION['flash_success'];
		unset($_SESSION['flash_success']);
	}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>ALECSO — Affectation</title>
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="shortcut icon" href="images/logo_alec.jpg" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --green-dark:   #1a6b3a;
      --green-main:   #23894e;
      --green-light:  #e8f5ee;
      --green-mid:    #c2e0cf;
      --red:          #de1d34;
      --gold:         #f0a500;
      --text-dark:    #1a2e22;
      --text-mid:     #4a6657;
      --text-light:   #8aab96;
      --white:        #ffffff;
      --card-shadow:  0 8px 40px rgba(26,107,58,.12);
      --radius:       16px;
    }

    body { font-family: 'DM Sans', sans-serif; background: #f2f7f4; }

    /* ── TOAST ─────────────────────────────────────────── */
    .toast-wrap {
      position: fixed; top: 28px; right: 28px; z-index: 9999;
      display: flex; flex-direction: column; gap: 12px; pointer-events: none;
    }
    .toast-pill {
      display: flex; align-items: center; gap: 14px;
      padding: 16px 22px; border-radius: 50px;
      font-size: 14px; font-weight: 600; letter-spacing: .02em;
      box-shadow: 0 12px 40px rgba(0,0,0,.18);
      transform: translateX(120px); opacity: 0;
      transition: transform .45s cubic-bezier(.34,1.56,.64,1), opacity .35s;
      pointer-events: auto; min-width: 280px;
    }
    .toast-pill.show { transform: translateX(0); opacity: 1; }
    .toast-pill.toast-success { background: var(--green-main); color: #fff; }
    .toast-pill.toast-error   { background: var(--red);        color: #fff; }
    .toast-pill .t-icon { font-size: 22px; flex-shrink: 0; }
    .toast-pill .t-msg  { flex: 1; }
    .toast-pill .t-close {
      background: none; border: none; color: rgba(255,255,255,.7);
      cursor: pointer; font-size: 18px; padding: 0; line-height: 1;
    }
    .toast-pill .t-close:hover { color: #fff; }

    /* ── CARD PRINCIPALE ────────────────────────────────── */
    .aff-card {
      background: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--card-shadow);
      overflow: hidden;
      margin-top: 24px;
    }

    /* bandeau titre */
    .aff-header {
      background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-main) 100%);
      padding: 28px 36px;
      display: flex; align-items: center; gap: 16px;
    }
    .aff-header .h-icon {
      width: 52px; height: 52px; border-radius: 50%;
      background: rgba(255,255,255,.18);
      display: flex; align-items: center; justify-content: center;
      font-size: 26px; color: #fff; flex-shrink: 0;
    }
    .aff-header h2 {
      font-family: 'DM Serif Display', serif;
      font-size: 24px; color: #fff; margin: 0; line-height: 1.2;
    }
    .aff-header p { margin: 2px 0 0; font-size: 13px; color: rgba(255,255,255,.72); }

    /* corps */
    .aff-body { padding: 32px 36px; }

    /* ── FICHE RÉCLAMATION ──────────────────────────────── */
    .claim-info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
      gap: 14px;
      margin-bottom: 32px;
    }
    .info-chip {
      background: var(--green-light);
      border: 1px solid var(--green-mid);
      border-radius: 12px;
      padding: 14px 18px;
      display: flex; flex-direction: column; gap: 4px;
    }
    .info-chip .ic-label {
      font-size: 11px; font-weight: 600; letter-spacing: .08em;
      text-transform: uppercase; color: var(--text-light);
    }
    .info-chip .ic-value {
      font-size: 15px; font-weight: 600; color: var(--text-dark);
    }

    /* badge statut inline */
    .status-pill {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 3px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;
    }
    .status-pill.pending     { background: #fff0f0; color: var(--red); }
    .status-pill.in-progress { background: #fff8e0; color: #a07000; }
    .status-pill.completed   { background: #e8f5ee; color: var(--green-dark); }

    /* badge dégâts */
    .dmg-pill {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 3px 12px; border-radius: 20px; font-size: 13px; font-weight: 600;
    }
    .dmg-pill.low    { background: #e8f5ee; color: var(--green-dark); }
    .dmg-pill.medium { background: #fff8e0; color: #a07000; }
    .dmg-pill.high   { background: #fff0f0; color: var(--red); }

    /* ── SÉPARATEUR ─────────────────────────────────────── */
    .section-divider {
      display: flex; align-items: center; gap: 14px;
      margin: 0 0 24px;
    }
    .section-divider span {
      font-size: 13px; font-weight: 700; letter-spacing: .07em;
      text-transform: uppercase; color: var(--text-light); white-space: nowrap;
    }
    .section-divider::before, .section-divider::after {
      content: ''; flex: 1; height: 1px; background: var(--green-mid);
    }

    /* ── GRILLE CHECKBOXES ──────────────────────────────── */
    .team-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 12px;
      margin-bottom: 32px;
    }
    .team-card {
      border: 2px solid var(--green-mid);
      border-radius: 12px;
      padding: 14px 16px;
      display: flex; align-items: center; gap: 12px;
      cursor: pointer;
      transition: border-color .2s, background .2s, transform .15s, box-shadow .2s;
      background: var(--white);
      user-select: none;
    }
    .team-card:hover {
      border-color: var(--green-main);
      background: var(--green-light);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(35,137,78,.13);
    }
    .team-card.selected {
      border-color: var(--green-main);
      background: var(--green-light);
      box-shadow: 0 4px 16px rgba(35,137,78,.18);
    }
    .team-card input[type="checkbox"] { display: none; }
    .tc-avatar {
      width: 40px; height: 40px; border-radius: 50%;
      background: var(--green-main);
      display: flex; align-items: center; justify-content: center;
      font-size: 17px; font-weight: 700; color: #fff; flex-shrink: 0;
      transition: background .2s;
    }
    .team-card.selected .tc-avatar { background: var(--green-dark); }
    .tc-name { font-size: 14px; font-weight: 600; color: var(--text-dark); flex: 1; }
    .tc-check {
      width: 22px; height: 22px; border-radius: 6px;
      border: 2px solid var(--green-mid);
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; color: transparent;
      transition: background .2s, border-color .2s, color .2s;
      flex-shrink: 0;
    }
    .team-card.selected .tc-check {
      background: var(--green-main); border-color: var(--green-main); color: #fff;
    }

    /* ── BOUTONS D'ACTION ───────────────────────────────── */
    .action-bar {
      display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
      padding-top: 8px; border-top: 1px solid var(--green-mid);
    }
    .btn-save {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 11px 28px; border-radius: 10px; border: none; cursor: pointer;
      font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 600;
      background: linear-gradient(135deg, var(--green-main), var(--green-dark));
      color: #fff;
      transition: transform .15s, box-shadow .2s;
      box-shadow: 0 4px 16px rgba(35,137,78,.3);
    }
    .btn-save:hover  { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(35,137,78,.4); }
    .btn-save:active { transform: translateY(0); }
    .btn-back {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 11px 24px; border-radius: 10px;
      border: 2px solid var(--green-mid); background: var(--white);
      font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 600;
      color: var(--text-mid); text-decoration: none;
      transition: border-color .2s, color .2s, background .2s;
    }
    .btn-back:hover { border-color: var(--green-main); color: var(--green-dark); background: var(--green-light); }

    /* ── SÉLECTION COMPTEUR ─────────────────────────────── */
    .selection-count {
      margin-left: auto;
      font-size: 13px; font-weight: 500; color: var(--text-light);
      display: flex; align-items: center; gap: 6px;
    }
    .selection-count strong { color: var(--green-main); font-size: 15px; }
  </style>
</head>

<body>

<!-- ── TOAST CONTAINER ──────────────────────────────────── -->
<div class="toast-wrap" id="toastWrap"></div>

<!-- ── NAVBAR ───────────────────────────────────────────── -->
<div class="horizontal-menu">
  <nav class="navbar top-navbar col-lg-12 col-12 p-0">
    <div class="container-fluid">
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-between">
        <ul class="navbar-nav navbar-nav-left">
          <li class="nav-item dropdown d-lg-flex d-none">
            <a class="dropdown-toggle show-dropdown-arrow btn btn-inverse-primary btn-sm"
               id="nreportDropdown" href="#" data-bs-toggle="dropdown"
               style="background-color:#23894e;color:white">
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
            <img src="http://localhost/reclamation/images/auth/logo_alec.jpg" alt="logo" style="width:200px;height:78px">
          </a>
          <a class="navbar-brand brand-logo-mini" href="index.html">
            <img src="http://localhost/reclamation/images/auth/logo_alec.jpg" alt="logo" style="width:200px;height:78px">
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
                Déconnexion
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
              <li class="nav-item"><a class="nav-link" href="http://localhost/reclamation/register-2.php">Register</a></li>
              <li class="nav-item"><a class="nav-link" href="http://localhost/reclamation/login.php">Login</a></li>
            </ul>
          </div>
        </li>
      </ul>
    </div>
  </nav>
</div>

<!-- ── CONTENU ───────────────────────────────────────────── -->
<div class="container-fluid page-body-wrapper">
  <div class="main-panel">
    <div class="content-wrapper" style="background:url(http://localhost/reclamation/images/auth/grey1.jpg);background-size:170px;">
      <div class="row">
        <div class="col-12 grid-margin">

          <div class="aff-card">

            <!-- En-tête -->
            <div class="aff-header">
              <div class="h-icon"><i class="mdi mdi-account-multiple-plus"></i></div>
              <div>
                <h2>Affectation de l'équipe</h2>
                <p>Sélectionnez les membres à affecter à cette réclamation</p>
              </div>
            </div>

            <div class="aff-body">

              <?php
              if(isset($_GET['id_rec']) && !empty($_GET['id_rec'])) {
                $id_rec = intval($_GET['id_rec']);
                $sql    = "SELECT C.*, U.full_name, U.departement, T.type FROM claim C, users U, type_reclamation T WHERE C.user_id=U.id AND C.id_type=T.id AND C.id=$id_rec";
                $result = mysqli_query($conn, $sql);
                if($row = mysqli_fetch_assoc($result)) {
              ?>

              <!-- Fiche réclamation -->
              <div class="section-divider"><span>Détails de la réclamation</span></div>

              <div class="claim-info-grid">
                <div class="info-chip">
                  <span class="ic-label"><i class="mdi mdi-account-outline"></i> Nom complet</span>
                  <span class="ic-value"><?php echo htmlspecialchars($row['full_name']); ?></span>
                </div>
                <div class="info-chip">
                  <span class="ic-label"><i class="mdi mdi-tag-outline"></i> Type</span>
                  <span class="ic-value"><?php echo htmlspecialchars($row['type']); ?></span>
                </div>
                <div class="info-chip">
                  <span class="ic-label"><i class="mdi mdi-map-marker-outline"></i> Lieu</span>
                  <span class="ic-value"><?php echo htmlspecialchars($row['place'] ?? '—'); ?></span>
                </div>
                <div class="info-chip">
                  <span class="ic-label"><i class="mdi mdi-alert-outline"></i> Dégâts</span>
                  <span class="ic-value">
                    <?php
                    if($row['damage'] == 'very little') echo '<span class="dmg-pill low"><i class="mdi mdi-chevron-down"></i> Très faible</span>';
                    elseif($row['damage'] == 'little')  echo '<span class="dmg-pill medium"><i class="mdi mdi-minus"></i> Faible</span>';
                    else                                echo '<span class="dmg-pill high"><i class="mdi mdi-chevron-up"></i> Élevée</span>';
                    ?>
                  </span>
                </div>
                <div class="info-chip">
                  <span class="ic-label"><i class="mdi mdi-flag-outline"></i> Statut</span>
                  <span class="ic-value">
                    <?php
                    if($row['status'] == 'pending')     echo '<span class="status-pill pending"><i class="mdi mdi-clock-outline"></i> En attente</span>';
                    elseif($row['status'] == 'Completed') echo '<span class="status-pill completed"><i class="mdi mdi-check-circle-outline"></i> Terminée</span>';
                    else                                echo '<span class="status-pill in-progress"><i class="mdi mdi-progress-clock"></i> En cours</span>';
                    ?>
                  </span>
                </div>
                <div class="info-chip" style="grid-column: span 2;">
                  <span class="ic-label"><i class="mdi mdi-text-box-outline"></i> Description</span>
                  <span class="ic-value" style="font-weight:400;font-size:14px;color:var(--text-mid);">
                    <?php echo htmlspecialchars($row['description'] ?? '—'); ?>
                  </span>
                </div>
              </div>

              <!-- Formulaire d'affectation -->
              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="id_rec" value="<?php echo $id_rec; ?>">

                <div class="section-divider"><span>Membres de l'équipe</span></div>

                <?php
                // Récupérer les affectations existantes
                $sql_aff = "SELECT * FROM affect_rec WHERE id_claim = $id_rec";
                $res_aff = mysqli_query($conn, $sql_aff);
                $emp_affect = [];
                while($ra = mysqli_fetch_assoc($res_aff)) $emp_affect[] = $ra['id_employee'];

                // Récupérer les membres de l'équipe
                $sql_team = "SELECT * FROM users WHERE team_member = 1";
                $res_team = mysqli_query($conn, $sql_team);
                $members  = [];
                while($rm = mysqli_fetch_assoc($res_team)) $members[] = $rm;
                ?>

                <div class="team-grid" id="teamGrid">
                <?php foreach($members as $member):
                  $checked  = in_array($member['id'], $emp_affect);
                  $initials = strtoupper(mb_substr($member['full_name'], 0, 1));
                ?>
                  <label class="team-card <?php echo $checked ? 'selected' : ''; ?>" data-id="<?php echo $member['id']; ?>">
                    <input type="checkbox" name="employees[]"
                           value="<?php echo $member['id']; ?>"
                           <?php echo $checked ? 'checked' : ''; ?>>
                    <div class="tc-avatar"><?php echo $initials; ?></div>
                    <span class="tc-name"><?php echo htmlspecialchars($member['full_name']); ?></span>
                    <div class="tc-check"><i class="mdi mdi-check"></i></div>
                  </label>
                <?php endforeach; ?>
                </div>

                <div class="action-bar">
                  <button type="submit" name="submit" class="btn-save">
                    <i class="mdi mdi-content-save-outline"></i>
                    Enregistrer
                  </button>
                  <a href="trial.php" class="btn-back">
                    <i class="mdi mdi-arrow-left"></i>
                    Retour à la liste
                  </a>
                  <div class="selection-count">
                    <i class="mdi mdi-account-group-outline"></i>
                    <strong id="selCount"><?php echo count($emp_affect); ?></strong>&nbsp;sélectionné(s)
                  </div>
                </div>
              </form>

              <?php
                } else {
                  echo '<div style="text-align:center;padding:48px;color:var(--red);font-size:18px;font-weight:600;"><i class="mdi mdi-alert-circle-outline" style="font-size:40px;display:block;margin-bottom:12px;"></i>Réclamation introuvable</div>';
                }
              } else {
                echo '<div style="text-align:center;padding:48px;color:var(--red);font-size:18px;font-weight:600;"><i class="mdi mdi-help-circle-outline" style="font-size:40px;display:block;margin-bottom:12px;"></i>Réclamation inconnue</div>';
              }
              ?>

            </div><!-- /aff-body -->
          </div><!-- /aff-card -->

        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── JS DATA FROM PHP ─────────────────────────────────── -->
<script>
  const PHP_SUCCESS = <?php echo isset($right) && strlen($right) ? 'true' : 'false'; ?>;
  const PHP_ERROR   = <?php echo isset($error) && strlen($error) ? json_encode($error) : 'null'; ?>;
</script>

<script src="vendors/base/vendor.bundle.base.js"></script>
<script src="js/template.js"></script>

<script>
/* ── TOAST ENGINE ──────────────────────────────────────── */
function showToast(msg, type = 'success', duration = 4000) {
  const wrap = document.getElementById('toastWrap');
  const pill = document.createElement('div');
  pill.className = `toast-pill toast-${type}`;
  pill.innerHTML = `
    <span class="t-icon">
      <i class="mdi ${type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline'}"></i>
    </span>
    <span class="t-msg">${msg}</span>
    <button class="t-close" onclick="this.closest('.toast-pill').remove()">
      <i class="mdi mdi-close"></i>
    </button>`;
  wrap.appendChild(pill);

  // Déclencher l'animation
  requestAnimationFrame(() => requestAnimationFrame(() => pill.classList.add('show')));

  setTimeout(() => {
    pill.classList.remove('show');
    setTimeout(() => pill.remove(), 400);
  }, duration);
}

/* ── AFFICHER LES MESSAGES PHP AU CHARGEMENT ───────────── */
document.addEventListener('DOMContentLoaded', () => {

  if (PHP_SUCCESS) {
    showToast('✓ Équipe affectée avec succès — statut mis à jour', 'success', 5000);
  }
  if (PHP_ERROR) {
    showToast(PHP_ERROR, 'error', 5000);
  }

  /* ── INTERACTIVITÉ CHECKBOXES ────────────────────────── */
  const cards   = document.querySelectorAll('.team-card');
  const counter = document.getElementById('selCount');

  function updateCount() {
    const n = document.querySelectorAll('.team-card.selected').length;
    if(counter) counter.textContent = n;
  }

  cards.forEach(card => {
    card.addEventListener('click', () => {
      const cb = card.querySelector('input[type="checkbox"]');
      cb.checked = !cb.checked;
      card.classList.toggle('selected', cb.checked);
      updateCount();
    });
  });

  updateCount();
});
</script>

</body>
</html>