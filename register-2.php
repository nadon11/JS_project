<?php
	include("dbase.php");

	$error = '';
	$right = '';

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$departement = filter_input(INPUT_POST, "department", FILTER_SANITIZE_SPECIAL_CHARS);
		$full_name   = filter_input(INPUT_POST, "full_name",  FILTER_SANITIZE_SPECIAL_CHARS);
		$login       = filter_input(INPUT_POST, "login",      FILTER_SANITIZE_SPECIAL_CHARS);
		$password    = filter_input(INPUT_POST, "password",   FILTER_SANITIZE_SPECIAL_CHARS);

		$sql    = "SELECT * FROM departments WHERE id=$departement";
		$result = mysqli_query($conn, $sql);

		if(mysqli_num_rows($result) > 0){
			if(empty($full_name)){
				$error = "Veuillez saisir votre nom complet";
			} elseif(empty($login)){
				$error = "Veuillez saisir votre email";
			} else {
				$sql    = "SELECT * FROM users WHERE login='$login'";
				$result = mysqli_query($conn, $sql);
				if(mysqli_num_rows($result) > 0){
					$error = "Cet email est déjà utilisé";
				} elseif(strlen($password) < 4){
					$error = "Le mot de passe doit contenir au moins 4 caractères";
				} else {
					$sql = "INSERT INTO users (departement, full_name, login, password, code) VALUES ('$departement','$full_name','$login','$password','')";
					if(mysqli_query($conn, $sql)){
						$right = "Compte créé avec succès";
					} else {
						$error = "Une erreur est survenue lors de l'enregistrement";
					}
				}
			}
		} else {
			$error = "Veuillez choisir un service dans la liste";
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>MAINTENANT — Inscription</title>
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="shortcut icon" href="images/loggo.png" />
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

  <style>
    :root {
      --green-dark:  #1a6b3a;
      --green-main:  #23894e;
      --green-light: #e8f5ee;
      --green-mid:   #c2e0cf;
      --red:         #de1d34;
      --text-dark:   #1a2e22;
      --text-mid:    #4a6657;
      --text-light:  #8aab96;
      --white:       #ffffff;
      --card-shadow: 0 8px 48px rgba(10,40,20,.28);
      --radius:      16px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* page fixe — pas de scroll */
    html, body {
      height: 100%;
      overflow: hidden;
      font-family: 'DM Sans', sans-serif;
    }

    body {
      background: url(images/auth/reg_back.png) center/cover no-repeat;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    body::before {
      content: '';
      position: fixed; inset: 0;
      background: rgba(8, 35, 18, 0.52);
      backdrop-filter: blur(4px);
      z-index: 0;
    }

    /* ── TOAST ─────────────────────────────────────────── */
    .toast-wrap {
      position: fixed; top: 28px; right: 28px; z-index: 9999;
      display: flex; flex-direction: column; gap: 12px; pointer-events: none;
    }
    .toast-pill {
      display: flex; align-items: center; gap: 14px;
      padding: 16px 22px; border-radius: 50px;
      font-size: 14px; font-weight: 600; letter-spacing: .02em;
      box-shadow: 0 12px 40px rgba(0,0,0,.25);
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
      cursor: pointer; font-size: 18px; line-height: 1;
    }
    .toast-pill .t-close:hover { color: #fff; }

    /* ── CARD ───────────────────────────────────────────── */
    .reg-card {
      position: relative; z-index: 1;
      width: 100%; max-width: 460px;
      background: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--card-shadow);
      overflow: hidden;
    }

    /* bandeau avec logo centré */
    .reg-header {
      background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-main) 100%);
      padding: 28px 32px 24px;
      display: flex; flex-direction: column; align-items: center; gap: 14px;
      text-align: center;
    }
    .reg-header .logo-box {
      background: var(--white);
      border-radius: 12px;
      padding: 8px 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,.2);
      display: inline-flex; align-items: center; justify-content: center;
    }
    .reg-header .logo-box img {
      height: 54px; width: auto; display: block;
    }
    .reg-header h2 {
      font-family: 'DM Serif Display', serif;
      font-size: 22px; color: #fff; line-height: 1.2;
    }
    .reg-header p { font-size: 13px; color: rgba(255,255,255,.75); }

    /* corps — scrollable si petit écran */
    .reg-body {
      padding: 28px 32px;
      max-height: calc(100vh - 220px);
      overflow-y: auto;
    }
    .reg-body::-webkit-scrollbar { width: 4px; }
    .reg-body::-webkit-scrollbar-track { background: transparent; }
    .reg-body::-webkit-scrollbar-thumb { background: var(--green-mid); border-radius: 4px; }

    /* ── CHAMPS ─────────────────────────────────────────── */
    .field-group { margin-bottom: 18px; }
    .field-group label {
      display: block;
      font-size: 11px; font-weight: 700; letter-spacing: .07em;
      text-transform: uppercase; color: var(--text-light); margin-bottom: 6px;
    }
    .field-group label i { margin-right: 5px; font-size: 13px; }

    .input-wrap { position: relative; }

    .field-input {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid var(--green-mid); border-radius: 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 14px; color: var(--text-dark);
      background: var(--white);
      transition: border-color .2s, box-shadow .2s;
      outline: none;
    }
    .field-input:focus {
      border-color: var(--green-main);
      box-shadow: 0 0 0 4px rgba(35,137,78,.12);
    }
    .field-input::placeholder { color: var(--text-light); }

    .field-input.has-eye { padding-right: 46px; }
    .toggle-eye {
      position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer;
      color: var(--text-light); font-size: 19px;
      transition: color .2s; padding: 0; line-height: 1;
    }
    .toggle-eye:hover { color: var(--green-main); }

    select.field-input {
      cursor: pointer; appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24'%3E%3Cpath fill='%238aab96' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 13px center;
      padding-right: 38px;
    }

    /* ── BOUTON ─────────────────────────────────────────── */
    .btn-save {
      width: 100%;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      padding: 13px; border-radius: 10px; border: none; cursor: pointer;
      font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 600;
      background: linear-gradient(135deg, var(--green-main), var(--green-dark));
      color: #fff; margin-top: 6px;
      transition: transform .15s, box-shadow .2s;
      box-shadow: 0 4px 16px rgba(35,137,78,.3);
    }
    .btn-save:hover  { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(35,137,78,.4); }
    .btn-save:active { transform: translateY(0); }

    /* ── LIEN CONNEXION ─────────────────────────────────── */
    .login-link {
      text-align: center;
      font-size: 13px; color: var(--text-mid);
      margin-top: 16px; padding-top: 16px;
      border-top: 1px solid var(--green-mid);
    }
    .login-link a {
      color: var(--green-main); font-weight: 600; text-decoration: none;
    }
    .login-link a:hover { color: var(--green-dark); text-decoration: underline; }
  </style>
</head>

<body>

<!-- TOAST -->
<div class="toast-wrap" id="toastWrap"></div>

<div class="reg-card">

  <!-- En-tête avec logo intégré -->
  <div class="reg-header">
    
    <h2>Créer un compte</h2>
    <p>Rejoignez-nous — cela ne prend que quelques étapes</p>
  </div>

  <div class="reg-body">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="regForm">

      <!-- Nom complet -->
      <div class="field-group">
        <label><i class="mdi mdi-account-outline"></i>Nom complet</label>
        <input type="text" name="full_name" class="field-input"
               placeholder="ex : Mohamed Ben Ali"
               value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
      </div>

      <!-- Département -->
      <div class="field-group">
        <label><i class="mdi mdi-domain"></i>Département</label>
        <select name="department" class="field-input">
          <?php
          $sql_dep = "SELECT * FROM departments";
          $res_dep = mysqli_query($conn, $sql_dep);
          while($dep = mysqli_fetch_assoc($res_dep)){
            $sel = (isset($_POST['department']) && $_POST['department'] == $dep['id']) ? 'selected' : '';
            echo '<option '.$sel.' value="'.htmlspecialchars($dep['id']).'">'.htmlspecialchars($dep['name']).'</option>';
          }
          ?>
        </select>
      </div>

      <!-- Email -->
      <div class="field-group">
        <label><i class="mdi mdi-email-outline"></i>Email</label>
        <input type="email" name="login" class="field-input"
               placeholder="exemple@alecso.org"
               value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
      </div>

      <!-- Mot de passe -->
      <div class="field-group">
        <label><i class="mdi mdi-lock-outline"></i>Mot de passe</label>
        <div class="input-wrap">
          <input type="password" name="password" id="passwordInput" class="field-input has-eye"
                 placeholder="Minimum 4 caractères">
          <button type="button" class="toggle-eye" onclick="togglePwd()">
            <i class="mdi mdi-eye-outline" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" name="submit" class="btn-save">
        <i class="mdi mdi-account-check-outline"></i>
        Créer mon compte
      </button>

      <div class="login-link">
        Vous avez déjà un compte ? <a href="login.php">Connexion</a>
      </div>

    </form>
  </div>

</div><!-- /reg-card -->

<!-- JS DATA FROM PHP -->
<script>
  const PHP_SUCCESS = <?php echo strlen($right) ? 'true' : 'false'; ?>;
  const PHP_ERROR   = <?php echo strlen($error)  ? json_encode($error) : 'null'; ?>;
</script>

<script src="vendors/base/vendor.bundle.base.js"></script>
<script src="js/template.js"></script>

<script>
function showToast(msg, type = 'success', duration = 5000) {
  const wrap = document.getElementById('toastWrap');
  const pill = document.createElement('div');
  pill.className = `toast-pill toast-${type}`;
  pill.innerHTML = `
    <span class="t-icon"><i class="mdi ${type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline'}"></i></span>
    <span class="t-msg">${msg}</span>
    <button class="t-close" onclick="this.closest('.toast-pill').remove()"><i class="mdi mdi-close"></i></button>`;
  wrap.appendChild(pill);
  requestAnimationFrame(() => requestAnimationFrame(() => pill.classList.add('show')));
  setTimeout(() => { pill.classList.remove('show'); setTimeout(() => pill.remove(), 400); }, duration);
}

document.addEventListener('DOMContentLoaded', () => {
  if (PHP_SUCCESS) showToast('✓ Compte créé avec succès', 'success', 6000);
  if (PHP_ERROR)   showToast(PHP_ERROR, 'error', 6000);
});

function togglePwd() {
  const input = document.getElementById('passwordInput');
  const icon  = document.getElementById('eyeIcon');
  if(input.type === 'password'){
    input.type = 'text';
    icon.className = 'mdi mdi-eye-off-outline';
  } else {
    input.type = 'password';
    icon.className = 'mdi mdi-eye-outline';
  }
}
</script>

</body>
</html>
<?php mysqli_close($conn); ?>