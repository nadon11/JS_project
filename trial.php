<?php
	session_start();
	if(!isset($_SESSION['login']))
	{
		header('Location: login.php');
		die;
	}
	include("dbase.php");
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

  <style>
    .search-bar-wrapper {
      display: flex; align-items: center; gap: 16px; flex-wrap: wrap;
      margin-bottom: 18px; padding: 14px 20px;
      background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(35,137,78,.09);
    }
    .search-input-group { position: relative; flex: 1; min-width: 220px; }
    .search-icon {
      position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
      color: #23894e; font-size: 18px; pointer-events: none;
    }
    .search-field {
      width: 100%; padding: 9px 38px 9px 38px;
      border: 1.5px solid #d4e8db; border-radius: 8px; font-size: 14px;
      outline: none; transition: border-color .2s, box-shadow .2s;
      background: #f8fdf9; color: #1a3a26;
    }
    .search-field:focus { border-color: #23894e; box-shadow: 0 0 0 3px rgba(35,137,78,.13); background: #fff; }
    .btn-clear-search {
      position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
      background: none; border: none; color: #bbb; font-size: 17px;
      cursor: pointer; padding: 0; display: none; transition: color .2s;
    }
    .btn-clear-search:hover { color: #e53935; }
    .btn-clear-search.visible { display: block; }
    .filter-chips { display: flex; gap: 8px; flex-wrap: wrap; }
    .chip {
      padding: 5px 14px; border-radius: 20px; border: 1.5px solid #c8e6c9;
      background: #f1f8f4; color: #23894e; font-size: 13px; font-weight: 500;
      cursor: pointer; transition: all .18s;
    }
    .chip:hover, .chip.active             { background: #23894e; color: #fff; border-color: #23894e; }
    .chip-danger  { border-color: #ee5b5b; color: #ee5b5b; background: #fff5f5; }
    .chip-danger.active,  .chip-danger:hover  { background: #ee5b5b; border-color: #ee5b5b; color: #fff; }
    .chip-warning { border-color: #fcd53b; color: #b87d00; background: #fffde7; }
    .chip-warning.active, .chip-warning:hover { background: #f5a623; border-color: #f5a623; color: #fff; }
    .chip-success { border-color: #0ddbb9; color: #00877a; background: #e0fdf8; }
    .chip-success.active, .chip-success:hover { background: #0ddbb9; border-color: #0ddbb9; color: #fff; }
    .no-results-row td { text-align: center; padding: 28px; color: #bbb; font-size: 15px; font-style: italic; }
    tr.filter-match td { background: rgba(35,137,78,.06) !important; transition: background .3s; }

    .delete-modal-content  { border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,.18); }
    .delete-modal-header   { background: linear-gradient(135deg,#ff6b6b,#de1d34); border: none; justify-content: center; padding: 28px 0 18px; }
    .delete-modal-icon     { font-size: 54px; color: #fff; animation: pulse-icon .8s ease infinite alternate; }
    @keyframes pulse-icon  { from { transform: scale(1); } to { transform: scale(1.1); } }
    .delete-modal-title    { font-size: 20px; font-weight: 700; color: #1a1a2e; margin-bottom: 10px; }
    .delete-modal-desc     { font-size: 15px; color: #555; line-height: 1.6; }
    .delete-modal-warning  { font-size: 13px; color: #de1d34; font-weight: 500; margin-top: -4px; }
    .delete-modal-footer   { border: none; padding: 18px 24px 24px; gap: 12px; }
    .btn-cancel            { border-radius: 8px; padding: 8px 22px; font-size: 14px; font-weight: 500; }
    .btn-confirm-delete    {
      border-radius: 8px; padding: 8px 22px; font-size: 14px; font-weight: 600;
      background: linear-gradient(135deg,#ff6b6b,#de1d34); border: none;
      transition: transform .15s, box-shadow .15s;
    }
    .btn-confirm-delete:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(222,29,52,.35); }
    .btn-delete-claim { border-radius: 7px; font-size: 13px; padding: 5px 12px; transition: transform .15s; }
    .btn-delete-claim:hover { transform: translateY(-1px); }

    .toast-notif {
      position: fixed; bottom: 32px; right: 32px; z-index: 9999;
      display: flex; align-items: center; gap: 12px;
      background: #23894e; color: #fff;
      padding: 14px 20px 14px 16px; border-radius: 12px;
      box-shadow: 0 8px 32px rgba(35,137,78,.35);
      font-size: 14px; font-weight: 500; min-width: 260px;
      transform: translateY(100px); opacity: 0;
      transition: transform .35s cubic-bezier(.34,1.56,.64,1), opacity .3s;
      pointer-events: none;
    }
    .toast-notif.show      { transform: translateY(0); opacity: 1; pointer-events: auto; }
    .toast-notif.toast-error { background: #de1d34; box-shadow: 0 8px 32px rgba(222,29,52,.35); }
    .toast-icon  { font-size: 22px; }
    .toast-msg   { flex: 1; }
    .toast-close { background: none; border: none; color: rgba(255,255,255,.7); font-size: 16px; cursor: pointer; padding: 0; margin-left: 4px; }
    .toast-close:hover { color: #fff; }
  </style>
</head>

<body>

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


<!-- ============================================================
     SECTION : RESPONSABLE / ADMIN
============================================================ -->
<?php if($_SESSION['role'] == 'responsable' || $_SESSION['role'] == 'admin') { ?>
<div class="container-fluid page-body-wrapper">
  <div class="main-panel">
    <div class="content-wrapper" style="background:url(http://localhost:8888/reclamation/images/auth/grey1.jpg);background-size:170px;">
      <div class="row"><div class="col-sm-6 mb-4 mb-xl-0"></div></div>

      <?php
      $sql    = "SELECT COUNT(status) as id FROM claim";
      $result = mysqli_query($conn, $sql);
      $row    = mysqli_fetch_assoc($result);

      if($row['id'] > 0) {
        $sql    = "SELECT COUNT(status) as id FROM claim";
        $result = mysqli_query($conn, $sql);
        $r      = mysqli_fetch_assoc($result);
        $stat   = $r['id'];

        $sql    = "SELECT COUNT(status) as id FROM claim WHERE status='pending'";
        $result = mysqli_query($conn, $sql);
        $r      = mysqli_fetch_assoc($result);
        $pend   = $r['id']; $repend = ($pend / $stat) * 100;

        $sql    = "SELECT COUNT(status) as id FROM claim WHERE status='In progress'";
        $result = mysqli_query($conn, $sql);
        $r      = mysqli_fetch_assoc($result);
        $prog   = $r['id']; $reprog = ($prog / $stat) * 100;

        $sql    = "SELECT COUNT(status) as id FROM claim WHERE status='Completed'";
        $result = mysqli_query($conn, $sql);
        $r      = mysqli_fetch_assoc($result);
        $comp   = $r['id']; $recomp = ($comp / $stat) * 100;
      ?>

      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">

            <div style="display:flex;justify-content:center;width:100%;">
              <div style="display:flex;gap:40px;align-items:flex-end;width:450px;justify-content:center;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <p style="font-size:15px;font-weight:600;color:#ee5b5b;margin:0;text-align:center;">En attente<br><?php echo round($repend, 1) ?>%</p>
                  <div id="circleProgress1"></div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <p style="font-size:15px;font-weight:600;color:#d4a900;margin:0;text-align:center;">En cours<br><?php echo round($reprog, 1) ?>%</p>
                  <div id="circleProgress2"></div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <p style="font-size:15px;font-weight:600;color:#0aaa90;margin:0;text-align:center;">Terminée<br><?php echo round($recomp, 1) ?>%</p>
                  <div id="circleProgress3"></div>
                </div>
              </div>
            </div>

            <h4 class="card-title" style="margin-top:59px;text-align:center;font-size:25px;">Tableau des demandes</h4>

            <div class="search-bar-wrapper">
              <div class="search-input-group">
                <span class="search-icon"><i class="mdi mdi-magnify"></i></span>
                <input type="text" id="searchResponsable" class="search-field" placeholder="Rechercher par nom, type, équipe...">
                <button class="btn-clear-search" id="clearSearchResponsable" title="Effacer"><i class="mdi mdi-close-circle"></i></button>
              </div>
              <div class="filter-chips">
                <button class="chip active"       data-filter="all"         data-target="responsable">Tous</button>
                <button class="chip chip-danger"  data-filter="pending"     data-target="responsable">En attente</button>
                <button class="chip chip-warning" data-filter="In progress" data-target="responsable">En cours</button>
                <button class="chip chip-success" data-filter="Completed"   data-target="responsable">Terminée</button>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-striped" id="tableResponsable">
                <thead>
                  <tr style="text-align:center">
                    <th style="font-size:18px;">Nom complet</th>
                    <th style="font-size:18px;">Dégâts</th>
                    <th style="font-size:18px;">Etat</th>
                    <th style="font-size:18px;">Date de reclamation</th>
                    <th style="font-size:18px;">Type</th>
                    <?php if($_SESSION['role'] == 'responsable' || $_SESSION['role'] == 'admin') { ?>
                    <th style="font-size:18px;">Equipe</th>
                    <?php } ?>
                    <?php if($_SESSION['role'] == 'admin') { ?>
                    <th style="font-size:18px;">Action</th>
                    <?php } ?>
                  </tr>
                </thead>
                <tbody style="text-align:center">
                <?php
                $sql    = "SELECT C.*, U.full_name FROM affect_rec C, users U WHERE C.id_employee = U.id";
                $result = mysqli_query($conn, $sql);
                $emp_affect = [];
                while($r = mysqli_fetch_assoc($result)) {
                  if(!isset($emp_affect[$r['id_claim']])) $emp_affect[$r['id_claim']] = [];
                  $emp_affect[$r['id_claim']][] = $r['full_name'];
                }
                $sql    = "SELECT C.*, U.full_name, U.departement, T.type FROM claim C, users U, type_reclamation T WHERE C.user_id = U.id AND C.id_type = T.id";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                ?>
                  <tr data-status="<?php echo htmlspecialchars($row['status']); ?>">
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td>
                      <?php if($row['damage'] == 'little') { ?>
                        <button type="button" class="btn btn-warning btn-rounded btn-fw">Faible</button>
                      <?php } elseif($row['damage'] == 'very little') { ?>
                        <button type="button" class="btn btn-success btn-rounded btn-fw">Très faible</button>
                      <?php } else { ?>
                        <button type="button" class="btn btn-danger btn-rounded btn-fw">Elevée</button>
                      <?php } ?>
                    </td>
                    <td>
                      <?php if($row['status'] == 'pending') { ?>
                        <label class="badge badge-danger">En attente</label>
                      <?php } elseif($row['status'] == 'Completed') { ?>
                        <label class="badge badge-success">Terminée</label>
                      <?php } else { ?>
                        <label class="badge badge-warning">En cours</label>
                      <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['date_add']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <?php if($_SESSION['role'] == 'responsable' || $_SESSION['role'] == 'admin') { ?>
                    <td>
                      <?php if(!isset($emp_affect[$row['id']])) { ?>
                        <a class="btn btn-inverse-primary btn-fw" href="affectation_rec.php?id_rec=<?php echo $row['id']; ?>">Ajouter</a>
                      <?php } else {
                        echo htmlspecialchars(implode(', ', $emp_affect[$row['id']])); ?>
                        <a class="btn btn-inverse-success btn-fw" href="affectation_rec.php?id_rec=<?php echo $row['id']; ?>">Modifier</a>
                      <?php } ?>
                    </td>
                    <?php } ?>
                    <?php if($_SESSION['role'] == 'admin') { ?>
                    <td>
                      <button type="button"
                        class="btn btn-danger btn-sm btn-delete-claim"
                        data-id="<?php echo $row['id']; ?>"
                        data-name="<?php echo htmlspecialchars($row['full_name']); ?>"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal">
                        <i class="mdi mdi-delete"></i> Supprimer
                      </button>
                    </td>
                    <?php } ?>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>

      <?php } else { ?>
        <div class="col-sm-6 col-md-4 col-lg-3" style="text-align:center;background:#ffffffba;width:50%;font-size:26px;margin-right:368px;margin-top:165px;border:2px solid #de1d34;color:#de1d34;">
          <i class="mdi mdi-alert-octagon"></i> Vous n'avez aucun formulaire
        </div>
      <?php } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ============================================================
     SECTION : TEAM
============================================================ -->
<?php } else if($_SESSION['role'] == 'team') { ?>
<div class="container-fluid page-body-wrapper">
  <div class="main-panel">
    <div class="content-wrapper" style="background:url(http://localhost:8888/reclamation/images/auth/grey1.jpg);background-size:170px;">

      <?php
      $sql    = "SELECT * FROM claim, affect_rec WHERE id_employee = ".$_SESSION['user_id'];
      $result = mysqli_query($conn, $sql);

      if(mysqli_num_rows($result) > 0) {
        $uid = $_SESSION['user_id'];

        $sql    = "SELECT COUNT(C.status) as id FROM claim C INNER JOIN affect_rec A ON A.id_claim = C.id WHERE A.id_employee=$uid";
        $result = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($result); $stat = $r['id'];

        $sql    = "SELECT COUNT(C.status) as id FROM claim C INNER JOIN affect_rec A ON A.id_claim = C.id WHERE C.status='pending' AND A.id_employee=$uid";
        $result = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($result); $pend=$r['id']; $repend=($stat > 0) ? ($pend/$stat)*100 : 0;

        $sql    = "SELECT COUNT(C.status) as id FROM claim C INNER JOIN affect_rec A ON A.id_claim = C.id WHERE C.status='In progress' AND A.id_employee=$uid";
        $result = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($result); $prog=$r['id']; $reprog=($stat > 0) ? ($prog/$stat)*100 : 0;

        $sql    = "SELECT COUNT(C.status) as id FROM claim C INNER JOIN affect_rec A ON A.id_claim = C.id WHERE C.status='Completed' AND A.id_employee=$uid";
        $result = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($result); $comp=$r['id']; $recomp=($stat > 0) ? ($comp/$stat)*100 : 0;
      ?>

      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">

            <!-- Cercles de progression team — même style que responsable/employee -->
            <div style="display:flex;justify-content:center;width:100%;">
              <div style="display:flex;gap:40px;align-items:flex-end;width:450px;justify-content:center;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <p style="font-size:15px;font-weight:600;color:#ee5b5b;margin:0;text-align:center;">En attente<br><?php echo round($repend,1) ?>%</p>
                  <div id="circleProgressT1"></div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <p style="font-size:15px;font-weight:600;color:#d4a900;margin:0;text-align:center;">En cours<br><?php echo round($reprog,1) ?>%</p>
                  <div id="circleProgressT2"></div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <p style="font-size:15px;font-weight:600;color:#0aaa90;margin:0;text-align:center;">Terminée<br><?php echo round($recomp,1) ?>%</p>
                  <div id="circleProgressT3"></div>
                </div>
              </div>
            </div>

            <h4 class="card-title" style="margin-top:59px;text-align:center;font-size:25px;">Tableau des demandes</h4>

            <div class="search-bar-wrapper">
              <div class="search-input-group">
                <span class="search-icon"><i class="mdi mdi-magnify"></i></span>
                <input type="text" id="searchTeam" class="search-field" placeholder="Rechercher par nom, type...">
                <button class="btn-clear-search" id="clearSearchTeam" title="Effacer"><i class="mdi mdi-close-circle"></i></button>
              </div>
              <div class="filter-chips">
                <button class="chip active"       data-filter="all"         data-target="team">Tous</button>
                <button class="chip chip-danger"  data-filter="pending"     data-target="team">En attente</button>
                <button class="chip chip-warning" data-filter="In progress" data-target="team">En cours</button>
                <button class="chip chip-success" data-filter="Completed"   data-target="team">Terminée</button>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-striped" id="tableTeam">
                <thead>
                  <tr style="text-align:center;">
                    <th style="font-size:18px;">Nom complet</th>
                    <th style="font-size:18px;">Dégâts</th>
                    <th style="font-size:18px;">Etat</th>
                    <th style="font-size:18px;">Date de reclamation</th>
                    <th style="font-size:18px;">Type</th>
                    <th style="font-size:18px;">Equipe</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $sql    = "SELECT C.*, U.full_name, U.id as id_user, U.departement, T.type, A.id_employee
                           FROM claim C, users U, type_reclamation T, affect_rec A
                           WHERE C.user_id=U.id AND C.id_type=T.id AND C.id=A.id_claim AND A.id_employee=".$_SESSION['user_id'];
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                ?>
                  <tr style="text-align:center;" data-status="<?php echo htmlspecialchars($row['status']); ?>">
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td>
                      <?php if($row['damage']=='little') { ?>
                        <button type="button" class="btn btn-warning btn-rounded btn-fw">Faible</button>
                      <?php } elseif($row['damage']=='very little') { ?>
                        <button type="button" class="btn btn-success btn-rounded btn-fw">Très faible</button>
                      <?php } else { ?>
                        <button type="button" class="btn btn-danger btn-rounded btn-fw">Elevée</button>
                      <?php } ?>
                    </td>
                    <td>
                      <?php if($row['status']=='pending') { ?>
                        <label class="badge badge-danger">En attente</label>
                      <?php } elseif($row['status']=='Completed') { ?>
                        <label class="badge badge-success">Terminée</label>
                      <?php } else { ?>
                        <label class="badge badge-warning">En cours</label>
                      <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['date_add']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td>
                      <?php if($row['status']=='Completed') { ?>
                        <a class="btn btn-link btn-fw" href="edit_rec.php?id_rec=<?php echo $row['id']; ?>">Consulter</a>
                      <?php } else { ?>
                        <a class="btn btn-inverse-success btn-fw" href="edit_rec.php?id_rec=<?php echo $row['id']; ?>">Modifier</a>
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>

      <?php } else { ?>
        <div class="col-sm-6 col-md-4 col-lg-3" style="text-align:center;background:#ffffffba;width:50%;font-size:26px;margin-right:368px;margin-top:165px;border:2px solid #de1d34;color:#de1d34;">
          <i class="mdi mdi-alert-octagon"></i> Vous n'avez aucun formulaire
        </div>
      <?php } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ============================================================
     SECTION : EMPLOYEE
============================================================ -->
<?php } else { // employee ?>
<div class="container-fluid page-body-wrapper">
  <div class="main-panel">
    <div class="content-wrapper" style="background:url(http://localhost:8888/reclamation/images/auth/grey1.jpg);background-size:170px;">

      <?php
      $sql    = "SELECT * FROM claim WHERE user_id=".$_SESSION['user_id'];
      $result = mysqli_query($conn, $sql);

      if(mysqli_num_rows($result) > 0) {
        $uid = $_SESSION['user_id'];

        $sql    = "SELECT COUNT(status) as id FROM claim WHERE user_id=$uid";
        $result = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($result); $stat = $r['id'];

        $sql    = "SELECT COUNT(status) as id FROM claim WHERE status='pending' AND user_id=$uid";
        $result = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($result); $pend=$r['id']; $repend=($pend/$stat)*100;

        $sql    = "SELECT COUNT(status) as id FROM claim WHERE status='In progress' AND user_id=$uid";
        $result = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($result); $prog=$r['id']; $reprog=($prog/$stat)*100;

        $sql    = "SELECT COUNT(status) as id FROM claim WHERE status='Completed' AND user_id=$uid";
        $result = mysqli_query($conn, $sql);
        $r = mysqli_fetch_assoc($result); $comp=$r['id']; $recomp=($comp/$stat)*100;
      ?>

      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">

            <div style="display:flex;justify-content:center;width:100%;padding-top:16px;">
              <div style="display:flex;gap:40px;align-items:flex-end;width:450px;justify-content:center;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <p style="font-size:15px;font-weight:600;color:#ee5b5b;margin:0;text-align:center;">En attente<br><?php echo round($repend,1) ?>%</p>
                  <div id="circleProgress4"></div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <p style="font-size:15px;font-weight:600;color:#d4a900;margin:0;text-align:center;">En cours<br><?php echo round($reprog,1) ?>%</p>
                  <div id="circleProgress5"></div>
                </div>
                <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                  <p style="font-size:15px;font-weight:600;color:#0aaa90;margin:0;text-align:center;">Terminée<br><?php echo round($recomp,1) ?>%</p>
                  <div id="circleProgress6"></div>
                </div>
              </div>
            </div>

            <h4 class="card-title" style="margin-top:59px;text-align:center;font-size:25px;">Tableau des demandes</h4>

            <div class="search-bar-wrapper">
              <div class="search-input-group">
                <span class="search-icon"><i class="mdi mdi-magnify"></i></span>
                <input type="text" id="searchEmployee" class="search-field" placeholder="Rechercher par lieu, type...">
                <button class="btn-clear-search" id="clearSearchEmployee" title="Effacer"><i class="mdi mdi-close-circle"></i></button>
              </div>
              <div class="filter-chips">
                <button class="chip active"       data-filter="all"         data-target="employee">Tous</button>
                <button class="chip chip-danger"  data-filter="pending"     data-target="employee">En attente</button>
                <button class="chip chip-warning" data-filter="In progress" data-target="employee">En cours</button>
                <button class="chip chip-success" data-filter="Completed"   data-target="employee">Terminée</button>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table table-striped" id="tableEmployee">
                <thead>
                  <tr style="text-align:center;">
                    <th style="font-size:18px;">Lieu</th>
                    <th style="font-size:18px;">Type</th>
                    <th style="font-size:18px;">Date de reclamation</th>
                    <th style="font-size:18px;">Dégâts</th>
                    <th style="font-size:18px;">Etat</th>
                    <th style="font-size:18px;">Equipe</th>
                    <th style="font-size:18px;">Tache terminée le</th>
                  </tr>
                </thead>
                <tbody style="text-align:center;">
                <?php
                $uid_emp = $_SESSION['user_id'];
                $sql    = "SELECT C.*, U.full_name FROM affect_rec C, users U WHERE C.id_employee = U.id";
                $result = mysqli_query($conn, $sql);
                $emp_affect = [];
                while($r = mysqli_fetch_assoc($result)) {
                  if(!isset($emp_affect[$r['id_claim']])) $emp_affect[$r['id_claim']] = [];
                  $emp_affect[$r['id_claim']][] = $r['full_name'];
                }
                $sql    = "SELECT C.*, U.full_name, U.id as id_user, T.type
                           FROM claim C
                           INNER JOIN users U ON U.id = C.user_id
                           INNER JOIN type_reclamation T ON T.id = C.id_type
                           WHERE C.user_id = $uid_emp";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                ?>
                  <tr data-status="<?php echo htmlspecialchars($row['status']); ?>">
                    <td><?php echo htmlspecialchars($row['place']); ?></td>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_add']); ?></td>
                    <td>
                      <?php if($row['damage']=='little') { ?>
                        <button type="button" class="btn btn-warning btn-rounded btn-fw">Faible</button>
                      <?php } elseif($row['damage']=='very little') { ?>
                        <button type="button" class="btn btn-success btn-rounded btn-fw">Très faible</button>
                      <?php } else { ?>
                        <button type="button" class="btn btn-danger btn-rounded btn-fw">Elevée</button>
                      <?php } ?>
                    </td>
                    <td>
                      <?php if($row['status']=='pending') { ?>
                        <label class="badge badge-danger">En attente</label>
                      <?php } elseif($row['status']=='Completed') { ?>
                        <label class="badge badge-success">Terminée</label>
                      <?php } else { ?>
                        <label class="badge badge-warning">En cours</label>
                      <?php } ?>
                    </td>
                    <td>
                      <?php
                      $cid = $row['id'];
                      if(isset($emp_affect[$cid]) && count($emp_affect[$cid]) > 0) {
                        echo htmlspecialchars(implode(', ', $emp_affect[$cid]));
                      } else {
                        echo '<span style="color:#bbb;font-style:italic;">Non affecté</span>';
                      }
                      ?>
                    </td>
                    <td>
                      <?php
                      if($row['status'] === 'Completed' && !empty($row['date_traitement'])) {
                        echo htmlspecialchars($row['date_traitement']);
                      } else {
                        echo '<span style="color:#bbb;font-style:italic;">—</span>';
                      }
                      ?>
                    </td>
                  </tr>
                <?php } ?>
                </tbody>
              </table>
            </div>

      <?php } else { ?>
        <div class="col-sm-6 col-md-4 col-lg-3" style="text-align:center;background:#ffffffba;width:50%;font-size:26px;margin-right:368px;margin-top:165px;border:2px solid #de1d34;color:#de1d34;">
          <i class="mdi mdi-alert-octagon" style="margin-right:-52px;"></i> Vous n'avez aucun formulaire
          <a class="btn btn-link btn-fw" href="trying.php" style="margin-top:50px;margin-right:-135px;font-size:17px;">Modifier</a>
        </div>
      <?php } } ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<!-- ============================================================
     MODALE SUPPRESSION (admin uniquement)
============================================================ -->
<?php if($_SESSION['role'] == 'admin') { ?>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content delete-modal-content">
      <div class="modal-header delete-modal-header">
        <div class="delete-modal-icon"><i class="mdi mdi-alert-circle-outline"></i></div>
      </div>
      <div class="modal-body text-center">
        <h5 class="delete-modal-title">Confirmer la suppression</h5>
        <p class="delete-modal-desc">Vous êtes sur le point de supprimer la réclamation de<br><strong id="deleteClaimName">—</strong></p>
        <p class="delete-modal-warning">Cette action est irréversible.</p>
      </div>
      <div class="modal-footer justify-content-center delete-modal-footer">
        <button type="button" class="btn btn-outline-secondary btn-cancel" data-bs-dismiss="modal">
          <i class="mdi mdi-close"></i> Annuler
        </button>
        <button type="button" class="btn btn-danger btn-confirm-delete" id="confirmDeleteBtn">
          <i class="mdi mdi-delete-forever"></i> Supprimer
        </button>
      </div>
    </div>
  </div>
</div>
<?php } ?>

<div id="toastNotif" class="toast-notif" role="alert" aria-live="polite">
  <span class="toast-icon"><i class="mdi mdi-check-circle-outline"></i></span>
  <span class="toast-msg" id="toastMsg">Opération réussie</span>
  <button class="toast-close" onclick="hideToast()"><i class="mdi mdi-close"></i></button>
</div>


<script src="vendors/base/vendor.bundle.base.js"></script>
<script src="js/template.js"></script>
<script src="vendors/progressbar.js/progressbar.min.js"></script>

<script>
/* ============================================================
   CERCLES — RESPONSABLE / ADMIN
============================================================ */
<?php if(($_SESSION['role'] == 'responsable' || $_SESSION['role'] == 'admin') && isset($repend)): ?>
if ($('#circleProgress1').length) {
  new ProgressBar.Circle(circleProgress1, { color:'#ee5b5b', strokeWidth:20, trailWidth:20, easing:'easeInOut', duration:1400, width:24 }).animate(<?php echo $repend/100 ?>);
}
if ($('#circleProgress2').length) {
  new ProgressBar.Circle(circleProgress2, { color:'#fcd53b', strokeWidth:20, trailWidth:20, easing:'easeInOut', duration:1400, width:42 }).animate(<?php echo $reprog/100 ?>);
}
if ($('#circleProgress3').length) {
  new ProgressBar.Circle(circleProgress3, { color:'#0ddbb9', strokeWidth:20, trailWidth:20, easing:'easeInOut', duration:1400, width:42 }).animate(<?php echo $recomp/100 ?>);
}
<?php endif; ?>

/* ============================================================
   CERCLES — TEAM
============================================================ */
<?php if($_SESSION['role'] == 'team' && isset($repend)): ?>
if ($('#circleProgressT1').length) {
  new ProgressBar.Circle(circleProgressT1, { color:'#ee5b5b', strokeWidth:20, trailWidth:20, easing:'easeInOut', duration:1400, width:24 }).animate(<?php echo $repend/100 ?>);
}
if ($('#circleProgressT2').length) {
  new ProgressBar.Circle(circleProgressT2, { color:'#fcd53b', strokeWidth:20, trailWidth:20, easing:'easeInOut', duration:1400, width:42 }).animate(<?php echo $reprog/100 ?>);
}
if ($('#circleProgressT3').length) {
  new ProgressBar.Circle(circleProgressT3, { color:'#0ddbb9', strokeWidth:20, trailWidth:20, easing:'easeInOut', duration:1400, width:42 }).animate(<?php echo $recomp/100 ?>);
}
<?php endif; ?>

/* ============================================================
   CERCLES — EMPLOYEE
============================================================ */
<?php if($_SESSION['role'] == 'employee' && isset($repend)): ?>
if ($('#circleProgress4').length) {
  new ProgressBar.Circle(circleProgress4, { color:'#ee5b5b', strokeWidth:20, trailWidth:20, easing:'easeInOut', duration:1400, width:24 }).animate(<?php echo $repend/100 ?>);
}
if ($('#circleProgress5').length) {
  new ProgressBar.Circle(circleProgress5, { color:'#fcd53b', strokeWidth:20, trailWidth:20, easing:'easeInOut', duration:1400, width:42 }).animate(<?php echo $reprog/100 ?>);
}
if ($('#circleProgress6').length) {
  new ProgressBar.Circle(circleProgress6, { color:'#0ddbb9', strokeWidth:20, trailWidth:20, easing:'easeInOut', duration:1400, width:42 }).animate(<?php echo $recomp/100 ?>);
}
<?php endif; ?>


/* ============================================================
   TOAST UTILITY
============================================================ */
let _toastTimer = null;
function showToast(msg, type = 'success') {
  const toast    = document.getElementById('toastNotif');
  const toastMsg = document.getElementById('toastMsg');
  const icon     = toast.querySelector('.toast-icon i');
  toastMsg.textContent = msg;
  toast.classList.remove('toast-error');
  if (type === 'error') {
    toast.classList.add('toast-error');
    icon.className = 'mdi mdi-alert-circle-outline';
  } else {
    icon.className = 'mdi mdi-check-circle-outline';
  }
  toast.classList.add('show');
  clearTimeout(_toastTimer);
  _toastTimer = setTimeout(hideToast, 3400);
}
function hideToast() {
  document.getElementById('toastNotif').classList.remove('show');
}


/* ============================================================
   FILTRE GÉNÉRIQUE
============================================================ */
function initTableSearch(searchInputId, clearBtnId, tableId, filterTarget) {
  const input    = document.getElementById(searchInputId);
  const clearBtn = document.getElementById(clearBtnId);
  const table    = document.getElementById(tableId);
  if (!input || !table) return;

  const tbody        = table.querySelector('tbody');
  let   activeFilter = 'all';
  let   noResultRow  = null;

  function filterRows() {
    const query = input.value.trim().toLowerCase();
    const rows  = Array.from(tbody.querySelectorAll('tr:not(.no-results-row)'));
    let   count = 0;
    rows.forEach(row => {
      const rowStatus   = (row.dataset.status || '').toLowerCase();
      const filterLower = activeFilter.toLowerCase();
      const matchStatus = activeFilter === 'all' || rowStatus === filterLower;
      const matchSearch = !query || row.innerText.toLowerCase().includes(query);
      if (matchStatus && matchSearch) {
        row.style.display = '';
        row.classList.toggle('filter-match', !!(query || activeFilter !== 'all'));
        count++;
      } else {
        row.style.display = 'none';
        row.classList.remove('filter-match');
      }
    });
    if (noResultRow) { noResultRow.remove(); noResultRow = null; }
    if (count === 0) {
      const cols = table.querySelectorAll('thead th').length;
      noResultRow = document.createElement('tr');
      noResultRow.className = 'no-results-row';
      noResultRow.innerHTML = `<td colspan="${cols}"><i class="mdi mdi-magnify-close" style="font-size:20px;vertical-align:middle;margin-right:6px;"></i>Aucun résultat trouvé</td>`;
      tbody.appendChild(noResultRow);
    }
    clearBtn.classList.toggle('visible', query.length > 0);
    return count;
  }

  let debounce;
  input.addEventListener('input', () => {
    clearTimeout(debounce);
    debounce = setTimeout(() => {
      const count = filterRows();
      if (input.value.trim()) {
        showToast(
          count > 0 ? `${count} résultat${count > 1 ? 's' : ''} trouvé${count > 1 ? 's' : ''}` : 'Aucun résultat pour cette recherche',
          count > 0 ? 'success' : 'error'
        );
      }
    }, 350);
  });

  clearBtn.addEventListener('click', () => {
    input.value = '';
    filterRows();
    showToast('Recherche effacée');
  });

  document.querySelectorAll(`.chip[data-target="${filterTarget}"]`).forEach(chip => {
    chip.addEventListener('click', () => {
      document.querySelectorAll(`.chip[data-target="${filterTarget}"]`).forEach(c => c.classList.remove('active'));
      chip.classList.add('active');
      activeFilter = chip.dataset.filter;
      const count  = filterRows();
      const label  = chip.textContent.trim();
      showToast(
        activeFilter === 'all'
          ? `Toutes les réclamations affichées (${count})`
          : `Filtre "${label}" — ${count} résultat${count > 1 ? 's' : ''}`,
        count > 0 ? 'success' : 'error'
      );
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initTableSearch('searchResponsable', 'clearSearchResponsable', 'tableResponsable', 'responsable');
  initTableSearch('searchTeam',        'clearSearchTeam',        'tableTeam',        'team');
  initTableSearch('searchEmployee',    'clearSearchEmployee',    'tableEmployee',    'employee');
});


/* ============================================================
   ADMIN — SUPPRESSION AJAX
============================================================ */
let _pendingDeleteId = null;

document.addEventListener('click', function(e) {
  const btn = e.target.closest('.btn-delete-claim');
  if (!btn) return;
  _pendingDeleteId = btn.dataset.id;
  document.getElementById('deleteClaimName').textContent = btn.dataset.name || '—';
});

document.getElementById('confirmDeleteBtn')?.addEventListener('click', function () {
  if (!_pendingDeleteId) return;
  const btn = this;
  btn.disabled = true;
  btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i> Suppression...';
  fetch('delete_claim.php', {
    method : 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body   : 'id=' + encodeURIComponent(_pendingDeleteId)
  })
  .then(res => res.json())
  .then(data => {
    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
    modal?.hide();
    if (data.success) {
      const row = document.querySelector(`.btn-delete-claim[data-id="${_pendingDeleteId}"]`)?.closest('tr');
      if (row) {
        row.style.transition = 'opacity .4s, transform .4s';
        row.style.opacity    = '0';
        row.style.transform  = 'translateX(40px)';
        setTimeout(() => row.remove(), 420);
      }
      showToast('Réclamation supprimée avec succès');
    } else {
      showToast(data.message || 'Erreur lors de la suppression', 'error');
    }
  })
  .catch(() => showToast('Erreur réseau. Veuillez réessayer.', 'error'))
  .finally(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="mdi mdi-delete-forever"></i> Supprimer';
    _pendingDeleteId = null;
  });
});
</script>

</body>
</html>