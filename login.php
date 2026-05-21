<?php
	session_start();
	include("dbase.php");
	if(isset($_SESSION['login']))
	{
		header('Location: trial.php');
		die;
	}

	$error = '';
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$login    = filter_input(INPUT_POST, "login",    FILTER_SANITIZE_SPECIAL_CHARS);
		$password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

		if(empty($login)){
			$error = 'Veuillez saisir le nom d\'utilisateur.';
		} else {
			$sql    = "SELECT * FROM users WHERE login = '$login' AND password = '$password'";
			$result = mysqli_query($conn, $sql);
			if(mysqli_num_rows($result) > 0){
				$row = mysqli_fetch_assoc($result);
				$_SESSION['mdp']          = $row["mdp"];
				$_SESSION['user_id']      = $row["id"];
				$_SESSION['full_name']    = $row["full_name"];
				$_SESSION['department']   = $row["department"];
				$_SESSION['role']         = $row["role"];
				if($row["team_member"] == 1 && $row["role"] == 'employee')
					$_SESSION['role'] = 'team';
				$_SESSION['login']        = $login;
				$_SESSION['connected_at'] = date('Y-m-d H:i:s');
				header('Location: trial.php');
				exit;
			} else {
				$sql    = "SELECT * FROM users WHERE login = '$login' AND code = '$password'";
				$result = mysqli_query($conn, $sql);
				if(mysqli_num_rows($result) > 0){
					$row = mysqli_fetch_assoc($result);
					$_SESSION['user_id']      = $row["id"];
					$_SESSION['full_name']    = $row["full_name"];
					$_SESSION['department']   = $row["department"];
					$_SESSION['role']         = $row["role"];
					if($row["team_member"] == 1 && $row["role"] == 'employee')
						$_SESSION['role'] = 'team';
					$_SESSION['login']        = $login;
					$_SESSION['connected_at'] = date('Y-m-d H:i:s');
					header('Location: mdp_oub.php');
					exit;
				} else {
					$error = 'Nom d\'utilisateur ou mot de passe incorrect.';
				}
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>MAINTENANT — Connexion</title>
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

    html, body {
      height: 100%;
      overflow: hidden;
      font-family: 'DM Sans', sans-serif;
    }

    body {
      background: url(images/auth/login_back.png) center/cover no-repeat;
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
    .toast-pill.toast-error   { background: var(--red);        color: #fff; }
    .toast-pill .t-icon { font-size: 22px; flex-shrink: 0; }
    .toast-pill .t-msg  { flex: 1; }
    .toast-pill .t-close {
      background: none; border: none; color: rgba(255,255,255,.7);
      cursor: pointer; font-size: 18px; line-height: 1;
    }
    .toast-pill .t-close:hover { color: #fff; }

    /* ── CARD deux colonnes ─────────────────────────────── */
    .login-card {
      position: relative; z-index: 1;
      width: 100%; max-width: 860px;
      display: flex;
      border-radius: var(--radius);
      box-shadow: var(--card-shadow);
      overflow: hidden;
      min-height: 520px;
    }

    /* ── COLONNE GAUCHE — formulaire ────────────────────── */
    .login-left {
      flex: 1;
      background: var(--white);
      padding: 48px 40px;
      display: flex; flex-direction: column; justify-content: center;
    }

    .brand { margin-bottom: 28px; }
    .brand h1 {
      font-family: 'DM Serif Display', serif;
      font-size: 22px; color: var(--green-dark); line-height: 1.25;
    }
    .brand p { font-size: 13px; color: var(--text-mid); margin-top: 5px; }

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

    /* case à cocher afficher mot de passe */
    .show-pwd-row {
      display: flex; align-items: center; gap: 8px;
      margin: -4px 0 16px; font-size: 13px; color: var(--text-mid);
      cursor: pointer;
    }
    .show-pwd-row input[type=checkbox] {
      accent-color: var(--green-main); width: 15px; height: 15px; cursor: pointer;
    }

    /* lien mot de passe oublié */
    .forgot-row {
      text-align: right; margin-bottom: 20px;
    }
    .forgot-row a {
      font-size: 13px; color: var(--green-main); font-weight: 600; text-decoration: none;
    }
    .forgot-row a:hover { color: var(--green-dark); text-decoration: underline; }

    /* ── BOUTON ─────────────────────────────────────────── */
    .btn-login {
      width: 100%;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      padding: 13px; border-radius: 10px; border: none; cursor: pointer;
      font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 600;
      background: linear-gradient(135deg, var(--green-main), var(--green-dark));
      color: #fff;
      transition: transform .15s, box-shadow .2s;
      box-shadow: 0 4px 16px rgba(35,137,78,.3);
    }
    .btn-login:hover  { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(35,137,78,.4); }
    .btn-login:active { transform: translateY(0); }

    /* ── LIEN INSCRIPTION ───────────────────────────────── */
    .register-link {
      text-align: center;
      font-size: 13px; color: var(--text-mid);
      margin-top: 16px; padding-top: 16px;
      border-top: 1px solid var(--green-mid);
    }
    .register-link a {
      color: var(--green-main); font-weight: 600; text-decoration: none;
    }
    .register-link a:hover { color: var(--green-dark); text-decoration: underline; }

    /* ── COLONNE DROITE — fond vert ────────────────────── */
    .login-right {
      flex: 1;
      background: linear-gradient(160deg, var(--green-main) 0%, var(--green-dark) 100%);
      position: relative;
      display: flex; flex-direction: column;
      align-items: center; justify-content: center;
      padding: 40px;
      overflow: hidden;
    }
    /* cercles décoratifs subtils */
    .login-right::before {
      content: '';
      position: absolute;
      width: 340px; height: 340px;
      border-radius: 50%;
      border: 60px solid rgba(255,255,255,.06);
      top: -80px; right: -80px;
    }
    .login-right::after {
      content: '';
      position: absolute;
      width: 260px; height: 260px;
      border-radius: 50%;
      border: 50px solid rgba(255,255,255,.05);
      bottom: -60px; left: -60px;
    }
    .login-right-content {
      position: relative; z-index: 1; text-align: center;
    }
    .login-right-content .icon-wrap {
      width: 72px; height: 72px; border-radius: 50%;
      background: rgba(255,255,255,.15);
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 22px;
    }
    .login-right-content .icon-wrap i { font-size: 34px; color: #fff; }
    .login-right-content h2 {
      font-family: 'DM Serif Display', serif;
      font-size: 26px; color: #fff; line-height: 1.3; margin-bottom: 12px;
    }
    .login-right-content p {
      font-size: 13px; color: rgba(255,255,255,.78); line-height: 1.7;
      max-width: 240px; margin: 0 auto 28px;
    }
    .login-right-content .dots { display: flex; gap: 8px; justify-content: center; }
    .login-right-content .dots span { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,.35); }
    .login-right-content .dots span:first-child { background: #fff; width: 22px; border-radius: 4px; }

    /* ── RESPONSIVE ─────────────────────────────────────── */
    @media (max-width: 700px) {
      .login-right { display: none; }
      .login-card  { max-width: 460px; }
      .login-left  { padding: 32px 24px; }
    }
  </style>
</head>

<body>

<!-- TOAST -->
<div class="toast-wrap" id="toastWrap"></div>

<div class="login-card">

  <!-- Colonne gauche : formulaire -->
  <div class="login-left">

    <div class="brand">
      <h1>MAINTENANT<br></h1>
      <p>Ravi de vous revoir !</p>
    </div>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="loginForm">

      <!-- Nom d'utilisateur -->
      <div class="field-group">
        <label><i class="mdi mdi-account-outline"></i>Nom d'utilisateur</label>
        <input type="text" name="login" class="field-input"
               placeholder="Votre identifiant"
               value="<?php echo isset($_POST['login']) ? htmlspecialchars($_POST['login']) : ''; ?>">
      </div>

      <!-- Mot de passe -->
      <div class="field-group">
        <label><i class="mdi mdi-lock-outline"></i>Mot de passe</label>
        <div class="input-wrap">
          <input type="password" name="password" id="passwordInput" class="field-input has-eye"
                 placeholder="Votre mot de passe"
                 value="<?php echo isset($_POST['password']) ? htmlspecialchars($_POST['password']) : ''; ?>">
          <button type="button" class="toggle-eye" onclick="togglePwd()">
            <i class="mdi mdi-eye-outline" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <!-- Afficher mot de passe -->
      <label class="show-pwd-row">
        <input type="checkbox" id="showPwdCheck" onchange="togglePwd()">
        Afficher le mot de passe
      </label>

      <!-- Mot de passe oublié -->
      <div class="forgot-row">
        <a href="mdp.php">Mot de passe oublié ?</a>
      </div>

      <button type="submit" name="submit" class="btn-login">
        <i class="mdi mdi-login"></i>
        Connexion
      </button>

      <div class="register-link">
        Vous n'avez pas de compte ? <a href="register-2.php">Créer un compte</a>
      </div>

    </form>
  </div>

  <!-- Colonne droite : fond vert -->
  <div class="login-right">
    <div class="login-right-content">
      <div class="icon-wrap">
        <i class="mdi mdi-shield-check-outline"></i>
      </div>
      <h2>Gérez vos réclamations simplement</h2>
      <p>Une plateforme centralisée pour suivre et traiter toutes vos demandes.</p>
      <div class="dots">
        <span></span><span></span><span></span>
      </div>
    </div>
  </div>

</div><!-- /login-card -->

<!-- JS DATA FROM PHP -->
<script>
  const PHP_ERROR = <?php echo strlen($error) ? json_encode($error) : 'null'; ?>;
</script>

<script src="vendors/base/vendor.bundle.base.js"></script>
<script src="js/template.js"></script>

<script>
function showToast(msg, type, duration) {
  duration = duration || 5000;
  var wrap = document.getElementById('toastWrap');
  var pill = document.createElement('div');
  pill.className = 'toast-pill toast-' + type;
  pill.innerHTML =
    '<span class="t-icon"><i class="mdi mdi-alert-circle-outline"></i></span>' +
    '<span class="t-msg">' + msg + '</span>' +
    '<button class="t-close" onclick="this.closest(\'.toast-pill\').remove()"><i class="mdi mdi-close"></i></button>';
  wrap.appendChild(pill);
  requestAnimationFrame(function(){ requestAnimationFrame(function(){ pill.classList.add('show'); }); });
  setTimeout(function(){
    pill.classList.remove('show');
    setTimeout(function(){ pill.remove(); }, 400);
  }, duration);
}

document.addEventListener('DOMContentLoaded', function() {
  if (PHP_ERROR) showToast(PHP_ERROR, 'error', 6000);
});

function togglePwd() {
  var input = document.getElementById('passwordInput');
  var icon  = document.getElementById('eyeIcon');
  var cb    = document.getElementById('showPwdCheck');
  var show  = input.type === 'password';
  input.type     = show ? 'text' : 'password';
  icon.className = show ? 'mdi mdi-eye-off-outline' : 'mdi mdi-eye-outline';
  cb.checked     = show;
}
</script>

</body>
</html>
<?php mysqli_close($conn); ?>