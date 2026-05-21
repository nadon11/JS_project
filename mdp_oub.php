<?php
	session_start();
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	if(!isset($_SESSION['login']))
	{
		header('Location: login.php');
		die;
	}
	include("dbase.php");

	$error = '';
	$right = '';

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$nv_mdp   = filter_input(INPUT_POST, "nv_mdp",   FILTER_SANITIZE_SPECIAL_CHARS);
		$renv_mdp = filter_input(INPUT_POST, "renv_mdp", FILTER_SANITIZE_SPECIAL_CHARS);

		if(empty($nv_mdp)){
			$error = "Veuillez saisir un nouveau mot de passe";
		} elseif(empty($renv_mdp)){
			$error = "Veuillez confirmer votre nouveau mot de passe";
		} elseif($nv_mdp !== $renv_mdp){
			$error = "Les deux mots de passe ne correspondent pas";
		} else {
			$sql = "UPDATE users SET `password`='".$nv_mdp."', code='', mdp=NULL WHERE id=".$_SESSION['user_id'];
			if(mysqli_query($conn, $sql)){
				$right = "Mot de passe mis à jour avec succès";
				header('Location: deconnexion.php');
				die;
			} else {
				$error = "Une erreur est survenue lors de la mise à jour";
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>ALECSO — Nouveau mot de passe</title>
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
      --card-shadow: 0 8px 40px rgba(26,107,58,.15);
      --radius:      16px;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'DM Sans', sans-serif;
      min-height: 100vh;
      background: url(http://localhost:8888/reclamation/images/auth/lock7.jpg) center/cover no-repeat fixed;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 32px 16px;
    }

    /* overlay sombre sur le bg */
    body::before {
      content: '';
      position: fixed; inset: 0;
      background: rgba(10, 40, 20, 0.52);
      backdrop-filter: blur(3px);
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

    /* ── WRAPPER CENTRÉ ─────────────────────────────────── */
    .page-wrapper {
      position: relative; z-index: 1;
      width: 100%; max-width: 480px;
      display: flex; flex-direction: column; align-items: center; gap: 28px;
    }

    /* ── LOGO ───────────────────────────────────────────── */
    .logo-wrap {
      text-align: center;
    }
    .logo-wrap img {
      height: 80px; width: auto;
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0,0,0,.35);
    }

    /* ── CARD ───────────────────────────────────────────── */
    .pwd-card {
      width: 100%;
      background: var(--white);
      border-radius: var(--radius);
      box-shadow: var(--card-shadow);
      overflow: hidden;
    }

    /* bandeau */
    .pwd-header {
      background: linear-gradient(135deg, var(--green-dark) 0%, var(--green-main) 100%);
      padding: 26px 32px;
      display: flex; align-items: center; gap: 16px;
    }
    .pwd-header .h-icon {
      width: 50px; height: 50px; border-radius: 50%;
      background: rgba(255,255,255,.18);
      display: flex; align-items: center; justify-content: center;
      font-size: 24px; color: #fff; flex-shrink: 0;
    }
    .pwd-header h2 {
      font-family: 'DM Serif Display', serif;
      font-size: 22px; color: #fff; line-height: 1.2;
    }
    .pwd-header p { font-size: 13px; color: rgba(255,255,255,.72); margin-top: 2px; }

    /* corps */
    .pwd-body { padding: 32px; }

    /* ── CHAMPS ─────────────────────────────────────────── */
    .field-group { margin-bottom: 22px; }
    .field-group label {
      display: block;
      font-size: 12px; font-weight: 700; letter-spacing: .07em;
      text-transform: uppercase; color: var(--text-light); margin-bottom: 8px;
    }
    .field-group label i { margin-right: 5px; font-size: 14px; }

    .input-wrap {
      position: relative;
    }
    .field-input {
      width: 100%;
      padding: 13px 48px 13px 16px;
      border: 2px solid var(--green-mid); border-radius: 10px;
      font-family: 'DM Sans', sans-serif;
      font-size: 15px; color: var(--text-dark);
      background: var(--white);
      transition: border-color .2s, box-shadow .2s;
      outline: none;
    }
    .field-input:focus {
      border-color: var(--green-main);
      box-shadow: 0 0 0 4px rgba(35,137,78,.12);
    }
    .field-input.input-error {
      border-color: var(--red);
      box-shadow: 0 0 0 4px rgba(222,29,52,.10);
    }
    .field-input::placeholder { color: var(--text-light); }

    /* bouton œil */
    .toggle-eye {
      position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
      background: none; border: none; cursor: pointer;
      color: var(--text-light); font-size: 20px;
      transition: color .2s; padding: 0; line-height: 1;
    }
    .toggle-eye:hover { color: var(--green-main); }

    /* indicateur force */
    .strength-bar {
      display: flex; gap: 4px; margin-top: 8px;
    }
    .strength-bar span {
      flex: 1; height: 4px; border-radius: 4px;
      background: var(--green-mid);
      transition: background .3s;
    }
    .strength-bar.s1 span:nth-child(1) { background: var(--red); }
    .strength-bar.s2 span:nth-child(1),
    .strength-bar.s2 span:nth-child(2) { background: #f0a500; }
    .strength-bar.s3 span:nth-child(1),
    .strength-bar.s3 span:nth-child(2),
    .strength-bar.s3 span:nth-child(3) { background: var(--green-main); }
    .strength-label {
      font-size: 11px; font-weight: 600; margin-top: 4px;
      color: var(--text-light); min-height: 16px;
    }

    /* message match */
    .match-msg {
      font-size: 12px; font-weight: 600; margin-top: 6px; min-height: 16px;
      display: flex; align-items: center; gap: 5px;
    }
    .match-msg.ok    { color: var(--green-main); }
    .match-msg.nope  { color: var(--red); }

    /* ── BOUTON ─────────────────────────────────────────── */
    .btn-save {
      width: 100%;
      display: flex; align-items: center; justify-content: center; gap: 8px;
      padding: 14px; border-radius: 10px; border: none; cursor: pointer;
      font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 600;
      background: linear-gradient(135deg, var(--green-main), var(--green-dark));
      color: #fff; margin-top: 8px;
      transition: transform .15s, box-shadow .2s;
      box-shadow: 0 4px 16px rgba(35,137,78,.3);
    }
    .btn-save:hover  { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(35,137,78,.4); }
    .btn-save:active { transform: translateY(0); }
    .btn-save:disabled { opacity: .55; cursor: not-allowed; transform: none; }
  </style>
</head>

<body>

<!-- TOAST -->
<div class="toast-wrap" id="toastWrap"></div>

<div class="page-wrapper">

  <!-- Logo -->
  <div class="logo-wrap">
    <img src="http://localhost:8888/reclamation/images/auth/logo_alec.jpg" alt="ALECSO">
  </div>

  <!-- Card -->
  <div class="pwd-card">

    <div class="pwd-header">
      <div class="h-icon"><i class="mdi mdi-lock-reset"></i></div>
      <div>
        <h2>Nouveau mot de passe</h2>
        <p>Choisissez un mot de passe sécurisé</p>
      </div>
    </div>

    <div class="pwd-body">
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="pwdForm">

        <!-- Nouveau MDP -->
        <div class="field-group">
          <label><i class="mdi mdi-lock-outline"></i>Nouveau mot de passe</label>
          <div class="input-wrap">
            <input type="password" name="nv_mdp" id="nv_mdp" class="field-input"
                   placeholder="Saisissez votre nouveau mot de passe"
                   autocomplete="new-password">
            <button type="button" class="toggle-eye" onclick="togglePwd('nv_mdp', this)">
              <i class="mdi mdi-eye-outline"></i>
            </button>
          </div>
          <!-- Barre de force -->
          <div class="strength-bar" id="strengthBar">
            <span></span><span></span><span></span>
          </div>
          <div class="strength-label" id="strengthLabel"></div>
        </div>

        <!-- Confirmer MDP -->
        <div class="field-group">
          <label><i class="mdi mdi-lock-check-outline"></i>Confirmer le mot de passe</label>
          <div class="input-wrap">
            <input type="password" name="renv_mdp" id="renv_mdp" class="field-input"
                   placeholder="Retapez votre mot de passe"
                   autocomplete="new-password">
            <button type="button" class="toggle-eye" onclick="togglePwd('renv_mdp', this)">
              <i class="mdi mdi-eye-outline"></i>
            </button>
          </div>
          <div class="match-msg" id="matchMsg"></div>
        </div>

        <button type="submit" name="submit" class="btn-save" id="btnSave">
          <i class="mdi mdi-content-save-outline"></i>
          Enregistrer
        </button>

      </form>
    </div>

  </div><!-- /pwd-card -->
</div><!-- /page-wrapper -->

<!-- JS DATA FROM PHP -->
<script>
  const PHP_ERROR   = <?php echo strlen($error) ? json_encode($error) : 'null'; ?>;
  const PHP_SUCCESS = <?php echo strlen($right)  ? 'true' : 'false'; ?>;
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
    <span class="t-icon"><i class="mdi ${type === 'success' ? 'mdi-check-circle-outline' : 'mdi-alert-circle-outline'}"></i></span>
    <span class="t-msg">${msg}</span>
    <button class="t-close" onclick="this.closest('.toast-pill').remove()"><i class="mdi mdi-close"></i></button>`;
  wrap.appendChild(pill);
  requestAnimationFrame(() => requestAnimationFrame(() => pill.classList.add('show')));
  setTimeout(() => { pill.classList.remove('show'); setTimeout(() => pill.remove(), 400); }, duration);
}

/* ── AFFICHER MESSAGES PHP ─────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  if (PHP_SUCCESS) showToast('✓ Mot de passe mis à jour avec succès', 'success', 5000);
  if (PHP_ERROR)   showToast(PHP_ERROR, 'error', 6000);
});

/* ── TOGGLE VISIBILITÉ MDP ─────────────────────────────── */
function togglePwd(id, btn) {
  const input = document.getElementById(id);
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'mdi mdi-eye-off-outline';
  } else {
    input.type = 'password';
    icon.className = 'mdi mdi-eye-outline';
  }
}

/* ── FORCE DU MOT DE PASSE ─────────────────────────────── */
function getStrength(pwd) {
  let score = 0;
  if (pwd.length >= 8)                    score++;
  if (/[A-Z]/.test(pwd) && /[a-z]/.test(pwd)) score++;
  if (/[0-9]/.test(pwd) || /[^A-Za-z0-9]/.test(pwd)) score++;
  return score;
}

const nv_mdp    = document.getElementById('nv_mdp');
const renv_mdp  = document.getElementById('renv_mdp');
const bar       = document.getElementById('strengthBar');
const label     = document.getElementById('strengthLabel');
const matchMsg  = document.getElementById('matchMsg');
const btnSave   = document.getElementById('btnSave');

const labels = ['', 'Faible', 'Moyen', 'Fort'];
const classes = ['', 's1', 's2', 's3'];

nv_mdp.addEventListener('input', () => {
  const s = getStrength(nv_mdp.value);
  bar.className = 'strength-bar ' + (nv_mdp.value.length ? classes[s] : '');
  label.textContent = nv_mdp.value.length ? labels[s] : '';
  checkMatch();
});

renv_mdp.addEventListener('input', checkMatch);

function checkMatch() {
  if (!renv_mdp.value.length) { matchMsg.textContent = ''; matchMsg.className = 'match-msg'; return; }
  if (nv_mdp.value === renv_mdp.value) {
    matchMsg.innerHTML = '<i class="mdi mdi-check-circle-outline"></i> Les mots de passe correspondent';
    matchMsg.className = 'match-msg ok';
    renv_mdp.classList.remove('input-error');
  } else {
    matchMsg.innerHTML = '<i class="mdi mdi-close-circle-outline"></i> Les mots de passe ne correspondent pas';
    matchMsg.className = 'match-msg nope';
    renv_mdp.classList.add('input-error');
  }
}
</script>

</body>
</html>