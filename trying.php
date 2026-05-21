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
		$status_array = array('pending', 'in progress', 'completed', 'not resolved');

		$place       = filter_input(INPUT_POST, "place",       FILTER_SANITIZE_SPECIAL_CHARS);
		$type        = filter_input(INPUT_POST, "type",        FILTER_SANITIZE_SPECIAL_CHARS);
		$damage      = filter_input(INPUT_POST, "damage",      FILTER_SANITIZE_SPECIAL_CHARS);
		$description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_SPECIAL_CHARS);

		$sql    = "SELECT * FROM type_reclamation WHERE id=$type";
		$result = mysqli_query($conn, $sql);

		if(mysqli_num_rows($result) > 0){
			if(empty($place)){
				$error = "Veuillez saisir l'emplacement du dégât";
			} elseif(empty($damage)){
				$error = "Veuillez choisir une option de dégât";
			} elseif(empty($description)){
				$error = "Veuillez saisir la description du dégât";
			} else {
				$sql = "INSERT INTO claim(user_id, id_empl, place, id_type, damage, description, description_affec, status)
				        VALUES (".$_SESSION['user_id'].", ".$_SESSION['user_id'].",'$place','$type','$damage','$description','','".$status_array[0]."')";
				if(mysqli_query($conn, $sql)){
					$right = "Réclamation enregistrée avec succès";
				} else {
					$error = "Une erreur est survenue lors de l'enregistrement";
				}
			}
		} else {
			$error = "Veuillez choisir un type de dégât valide";
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>MAINTENANT</title>
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="shortcut icon" href="images/loggo.png" />
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
      pointer-events: auto; min-width: 300px;
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
    .form-card {
      background: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--card-shadow);
      overflow: hidden;
      margin-top: 24px;
      max-width: 1346px;
      margin-left: auto;
      margin-right: auto;
    }

    /* bandeau titre */
    .form-header {
      background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-main) 100%);
      padding: 28px 36px;
      display: flex; align-items: center; gap: 16px;
    }
    .form-header .h-icon {
      width: 52px; height: 52px; border-radius: 50%;
      background: rgba(255,255,255,.18);
      display: flex; align-items: center; justify-content: center;
      font-size: 26px; color: #fff; flex-shrink: 0;
    }
    .form-header h2 {
      font-family: 'DM Serif Display', serif;
      font-size: 24px; color: #fff; margin: 0; line-height: 1.2;
    }
    .form-header p { margin: 2px 0 0; font-size: 13px; color: rgba(255,255,255,.72); }

    /* corps */
    .form-body { padding: 36px 40px; }

    /* ── SÉPARATEUR ─────────────────────────────────────── */
    .section-divider {
      display: flex; align-items: center; gap: 14px;
      margin: 0 0 28px;
    }
    .section-divider span {
      font-size: 13px; font-weight: 700; letter-spacing: .07em;
      text-transform: uppercase; color: var(--text-light); white-space: nowrap;
    }
    .section-divider::before, .section-divider::after {
      content: ''; flex: 1; height: 1px; background: var(--green-mid);
    }

    /* ── CHAMPS ─────────────────────────────────────────── */
    .field-group {
      margin-bottom: 24px;
    }
    .field-group label {
      display: block;
      font-size: 12px; font-weight: 700; letter-spacing: .07em;
      text-transform: uppercase; color: var(--text-light);
      margin-bottom: 8px;
    }
    .field-group label i { margin-right: 6px; font-size: 14px; }

    .field-input {
      width: 100%;
      padding: 13px 16px;
      border: 2px solid var(--green-mid);
      border-radius: 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 15px; color: var(--text-dark);
      background: var(--white);
      transition: border-color .2s, box-shadow .2s;
      outline: none;
      box-sizing: border-box;
    }
    .field-input:focus {
      border-color: var(--green-main);
      box-shadow: 0 0 0 4px rgba(35,137,78,.12);
    }
    .field-input::placeholder { color: var(--text-light); }

    select.field-input { cursor: pointer; appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24'%3E%3Cpath fill='%238aab96' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 14px center;
      padding-right: 40px;
    }

    textarea.field-input { resize: vertical; min-height: 110px; }

    /* ── RADIO DÉGÂTS ───────────────────────────────────── */
    .damage-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
    }
    .dmg-option {
      border: 2px solid var(--green-mid);
      border-radius: 12px;
      padding: 14px 12px;
      display: flex; flex-direction: column; align-items: center; gap: 8px;
      cursor: pointer;
      transition: border-color .2s, background .2s, transform .15s, box-shadow .2s;
      background: var(--white);
      user-select: none;
      text-align: center;
    }
    .dmg-option:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(35,137,78,.13);
    }
    .dmg-option input[type="radio"] { display: none; }
    .dmg-icon {
      width: 40px; height: 40px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; transition: background .2s;
    }
    .dmg-label {
      font-size: 13px; font-weight: 600; color: var(--text-mid);
      transition: color .2s;
    }
    /* élevée */
    .dmg-option.high .dmg-icon  { background: #fff0f0; color: var(--red); }
    .dmg-option.high:hover,
    .dmg-option.high.selected   { border-color: var(--red); background: #fff0f0; }
    .dmg-option.high.selected .dmg-label { color: var(--red); }
    /* faible */
    .dmg-option.medium .dmg-icon  { background: #fff8e0; color: #a07000; }
    .dmg-option.medium:hover,
    .dmg-option.medium.selected   { border-color: #f0a500; background: #fff8e0; }
    .dmg-option.medium.selected .dmg-label { color: #a07000; }
    /* très faible */
    .dmg-option.low .dmg-icon  { background: var(--green-light); color: var(--green-dark); }
    .dmg-option.low:hover,
    .dmg-option.low.selected   { border-color: var(--green-main); background: var(--green-light); }
    .dmg-option.low.selected .dmg-label { color: var(--green-dark); }

    /* ── BOUTONS ─────────────────────────────────────────── */
    .action-bar {
      display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
      padding-top: 28px; border-top: 1px solid var(--green-mid);
      margin-top: 8px;
    }
    .btn-save {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 12px 32px; border-radius: 10px; border: none; cursor: pointer;
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
      padding: 12px 24px; border-radius: 10px;
      border: 2px solid var(--green-mid); background: var(--white);
      font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 600;
      color: var(--text-mid); text-decoration: none;
      transition: border-color .2s, color .2s, background .2s;
    }
    .btn-back:hover { border-color: var(--green-main); color: var(--green-dark); background: var(--green-light); }

    /* ── RESET après succès ──────────────────────────────── */
    .field-input.success-anim {
      border-color: var(--green-main);
      box-shadow: 0 0 0 4px rgba(35,137,78,.15);
      animation: pulseGreen .6s ease;
    }
    @keyframes pulseGreen {
      0%   { box-shadow: 0 0 0 0 rgba(35,137,78,.4); }
      70%  { box-shadow: 0 0 0 10px rgba(35,137,78,0); }
      100% { box-shadow: 0 0 0 4px rgba(35,137,78,.15); }
    }

    @media (max-width: 576px) {
      .form-body { padding: 24px 20px; }
      .damage-grid { grid-template-columns: 1fr; }
    }
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
            <img src="http://localhost:8888/reclamation/images/auth/loggo.png" alt="logo" style="width:200px;height:78px">
          </a>
          <a class="navbar-brand brand-logo-mini" href="index.html">
            <img src="http://localhost:8888/reclamation/images/auth/loggo.png" alt="logo" style="width:200px;height:78px">
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
        <li class="nav-item">
          <a class="nav-link" href="trial.php">
            <i class="mdi mdi-file-document-box menu-icon"></i>
            <span class="menu-title">Accueil</span>
          </a>
        </li>
        <?php if($_SESSION['role'] == 'employee') { ?>
        <li class="nav-item active">
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

<!-- ── CONTENU ───────────────────────────────────────────── -->
<div class="container-fluid page-body-wrapper">
  <div class="main-panel">
    <div class="content-wrapper" style="background:url(http://localhost:8888/reclamation/images/auth/grey1.jpg);background-size:170px;">
      <div class="row">
        <div class="col-12 grid-margin">

          <div class="form-card">

            <!-- En-tête -->
            <div class="form-header">
              <div class="h-icon"><i class="mdi mdi-clipboard-text-outline"></i></div>
              <div>
                <h2>Formulaire de réclamation</h2>
                <p>Signalez un dégât ou une anomalie dans votre bâtiment</p>
              </div>
            </div>

            <div class="form-body">

              <div class="section-divider"><span>Informations du dégât</span></div>

              <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="claimForm">

                <!-- Emplacement -->
                <div class="field-group">
                  <label><i class="mdi mdi-map-marker-outline"></i>Emplacement du dégât</label>
                  <input type="text" name="place" id="place" class="field-input"
                         placeholder="ex : Bureau 3, Couloir B, Salle de réunion…"
                         value="<?php echo isset($_POST['place']) ? htmlspecialchars($_POST['place']) : ''; ?>">
                </div>

                <!-- Type -->
                <div class="field-group">
                  <label><i class="mdi mdi-tag-outline"></i>Type de dégât</label>
                  <select name="type" id="typeSelect" class="field-input">
                    <?php
                    $sql_types = "SELECT * FROM type_reclamation";
                    $res_types = mysqli_query($conn, $sql_types);
                    while($row = mysqli_fetch_assoc($res_types)){
                      $sel = (isset($_POST['type']) && $_POST['type'] == $row['id']) ? 'selected' : '';
                      echo '<option '.$sel.' value="'.htmlspecialchars($row['id']).'">'.htmlspecialchars($row['type']).'</option>';
                    }
                    ?>
                  </select>
                </div>

                <!-- Niveau de dégât -->
                <div class="field-group">
                  <label><i class="mdi mdi-alert-outline"></i>Niveau du dégât</label>
                  <div class="damage-grid">

                    <label class="dmg-option high <?php echo (isset($_POST['damage']) && $_POST['damage']=='عالية') ? 'selected' : ''; ?>">
                      <input type="radio" name="damage" value="عالية"
                             <?php echo (isset($_POST['damage']) && $_POST['damage']=='عالية') ? 'checked' : ''; ?>>
                      <div class="dmg-icon"><i class="mdi mdi-chevron-double-up"></i></div>
                      <span class="dmg-label">Élevée</span>
                    </label>

                    <label class="dmg-option medium <?php echo (isset($_POST['damage']) && $_POST['damage']=='قليل') ? 'selected' : ''; ?>">
                      <input type="radio" name="damage" value="قليل"
                             <?php echo (isset($_POST['damage']) && $_POST['damage']=='قليل') ? 'checked' : ''; ?>>
                      <div class="dmg-icon"><i class="mdi mdi-minus"></i></div>
                      <span class="dmg-label">Faible</span>
                    </label>

                    <label class="dmg-option low <?php echo (isset($_POST['damage']) && $_POST['damage']=='ضعيف') ? 'selected' : ''; ?>">
                      <input type="radio" name="damage" value="ضعيف"
                             <?php echo (isset($_POST['damage']) && $_POST['damage']=='ضعيف') ? 'checked' : ''; ?>>
                      <div class="dmg-icon"><i class="mdi mdi-chevron-double-down"></i></div>
                      <span class="dmg-label">Très faible</span>
                    </label>

                  </div>
                </div>

                <!-- Description -->
                <div class="field-group">
                  <label><i class="mdi mdi-text-box-outline"></i>Description du dégât</label>
                  <textarea name="description" id="description" class="field-input"
                            placeholder="Décrivez le problème en détail…"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>

                <!-- Boutons -->
                <div class="action-bar">
                  <button type="submit" name="submit" class="btn-save">
                    <i class="mdi mdi-content-save-outline"></i>
                    Enregistrer
                  </button>
                  <a href="trial.php" class="btn-back">
                    <i class="mdi mdi-arrow-left"></i>
                    Retour
                  </a>
                </div>

              </form>

            </div><!-- /form-body -->
          </div><!-- /form-card -->

        </div>
      </div>
    </div>
  </div>
</div>

<!-- ── JS DATA FROM PHP ─────────────────────────────────── -->
<script>
  const PHP_SUCCESS = <?php echo strlen($right) ? 'true' : 'false'; ?>;
  const PHP_ERROR   = <?php echo strlen($error) ? json_encode($error) : 'null'; ?>;
</script>

<script src="vendors/base/vendor.bundle.base.js"></script>
<script src="js/template.js"></script>

<script>
/* ── TOAST ENGINE ──────────────────────────────────────── */
function showToast(msg, type = 'success', duration = 5000) {
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
  requestAnimationFrame(() => requestAnimationFrame(() => pill.classList.add('show')));
  setTimeout(() => {
    pill.classList.remove('show');
    setTimeout(() => pill.remove(), 400);
  }, duration);
}

document.addEventListener('DOMContentLoaded', () => {

  /* ── Afficher les messages PHP ──────────────────────── */
  if (PHP_SUCCESS) {
    showToast('✓ Réclamation enregistrée avec succès', 'success', 6000);
    // Animation de confirmation sur les champs
    document.querySelectorAll('.field-input').forEach(el => {
      el.classList.add('success-anim');
    });
    // Réinitialiser le formulaire après un court délai
    setTimeout(() => {
      document.getElementById('claimForm').reset();
      document.querySelectorAll('.dmg-option').forEach(o => o.classList.remove('selected'));
      document.querySelectorAll('.field-input').forEach(el => el.classList.remove('success-anim'));
    }, 1200);
  }

  if (PHP_ERROR) {
    showToast(PHP_ERROR, 'error', 6000);
  }

  /* ── Interactivité options dégât ─────────────────────── */
  document.querySelectorAll('.dmg-option').forEach(option => {
    option.addEventListener('click', () => {
      document.querySelectorAll('.dmg-option').forEach(o => o.classList.remove('selected'));
      option.classList.add('selected');
      const radio = option.querySelector('input[type="radio"]');
      radio.checked = true;
    });
  });

});
</script>

</body>
</html>