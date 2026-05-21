<?php
	session_start();
	if(!isset($_SESSION['login']))
	{
		header('Location: try_log.php');
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
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    .team-wrapper {
      background: #ffffff;
      min-height: 500px;
      padding: 36px 28px;
      font-family: 'Cairo', sans-serif;
      border-radius: 12px;
      margin: 20px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    .team-title {
      text-align: center;
      color: #1aa957;
      font-size: 26px;
      font-weight: 700;
      letter-spacing: 1px;
      margin-bottom: 24px;
    }
    .controls {
      display: flex;
      gap: 12px;
      margin-bottom: 20px;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
    }
    .search-box {
      background: #f9fafb;
      border: 1px solid #d1d5db;
      border-radius: 8px;
      padding: 9px 16px;
      color: #111;
      font-size: 14px;
      font-family: 'Cairo', sans-serif;
      outline: none;
      flex: 1;
      min-width: 200px;
      transition: border 0.2s;
    }
    .search-box:focus {
      border-color: #1aa957;
      background: #fff;
    }
    .filter-btns { display: flex; gap: 8px; flex-wrap: wrap; }
    .filter-btn {
      background: #f3f4f6;
      border: 1px solid #d1d5db;
      color: #374151;
      font-size: 13px;
      font-family: 'Cairo', sans-serif;
      padding: 6px 16px;
      border-radius: 20px;
      cursor: pointer;
      transition: all 0.2s;
    }
    .filter-btn:hover { background: #d1fae5; border-color: #1aa957; color: #1aa957; }
    .filter-btn.active { background: #1aa957; border-color: #1aa957; color: #fff; font-weight: 700; }
    .result-count {
      color: #6b7280;
      font-size: 13px;
      white-space: nowrap;
    }
    .table-container {
      border-radius: 10px;
      overflow: hidden;
      border: 1px solid #e5e7eb;
    }
    .team-table { width: 100%; border-collapse: collapse; }
    .team-table thead tr { background: #1aa957; }
    .team-table th {
      padding: 15px 22px;
      font-size: 14px;
      font-weight: 700;
      color: #fff;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      cursor: pointer;
      user-select: none;
      font-family: 'Cairo', sans-serif;
    }
    .team-table th.no-sort { cursor: default; }
    .team-table th:not(.no-sort):hover { background: #17a050; }
    .sort-icon { margin-left: 6px; opacity: 0.8; font-size: 11px; }
    .team-table tbody tr {
      background: #fff;
      border-bottom: 1px solid #f0f0f0;
      transition: background 0.15s, transform 0.12s;
    }
    .team-table tbody tr:nth-child(even) { background: #f9fafb; }
    .team-table tbody tr:hover {
      background: #f0fdf4;
      transform: translateX(3px);
    }
    .team-table td {
      padding: 13px 22px;
      color: #111827;
      font-size: 14px;
      font-family: 'Cairo', sans-serif;
      vertical-align: middle;
    }
    .name-cell { display: flex; align-items: center; gap: 10px; }
    .avatar {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 34px; height: 34px;
      border-radius: 50%;
      font-weight: 700;
      font-size: 12px;
      flex-shrink: 0;
    }
    .dept-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      font-family: 'Cairo', sans-serif;
    }
    .btn-ajouter {
      display: inline-block;
      padding: 6px 16px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      font-family: 'Cairo', sans-serif;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s;
      background: #eff6ff;
      color: #2563eb;
      border: 1px solid #bfdbfe;
    }
    .btn-ajouter:hover {
      background: #2563eb;
      color: #fff;
      border-color: #2563eb;
    }
    .btn-supprimer {
      display: inline-block;
      padding: 6px 16px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      font-family: 'Cairo', sans-serif;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s;
      background: #fef2f2;
      color: #dc2626;
      border: 1px solid #fecaca;
    }
    .btn-supprimer:hover {
      background: #dc2626;
      color: #fff;
      border-color: #dc2626;
    }
    .btn-reinit {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      font-family: 'Cairo', sans-serif;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s;
      background: #fff7ed;
      color: #d97706;
      border: 1px solid #fed7aa;
    }
    .btn-reinit:hover {
      background: #d97706;
      color: #fff;
      border-color: #d97706;
    }
    .mdp-text {
      font-size: 12px;
      color: #6b7280;
      font-family: monospace;
      margin-right: 8px;
      background: #f3f4f6;
      padding: 2px 8px;
      border-radius: 4px;
    }
    .no-results {
      text-align: center;
      padding: 50px;
      color: #9ca3af;
      font-size: 15px;
      font-family: 'Cairo', sans-serif;
    }
    .team-badge-yes {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      background: #d1fae5;
      color: #065f46;
      border: 1px solid #6ee7b7;
    }
    .team-badge-no {
      display: inline-block;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      background: #f3f4f6;
      color: #6b7280;
      border: 1px solid #e5e7eb;
    }
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
              <a class="dropdown-item">
                <i class="mdi mdi-file-pdf text-primary"></i> Pdf
              </a>
              <a class="dropdown-item">
                <i class="mdi mdi-file-excel text-primary"></i> Excel
              </a>
            </div>
          </li>
        </ul>
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
          <a class="navbar-brand brand-logo">
            <img src="http://localhost/reclamation/images/auth/loggo.png" alt="logo" style="width:200px; height:78px">
          </a>
          <a class="navbar-brand brand-logo-mini" href="index.html">
            <img src="http://localhost/reclamation/images/auth/loggo.png" alt="logo" style="width:200px; height:78px">
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
        <li class="nav-item">
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
        <li class="nav-item active">
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

<!-- CONTENU PRINCIPAL -->
<div class="team-wrapper">
  <h2 class="team-title">Liste des Employés</h2>

  <div class="controls">
    <input class="search-box" id="searchInput" type="text" placeholder="🔍 Rechercher un employé ou service...">
    <div class="filter-btns" id="filterBtns"></div>
    <span class="result-count" id="resultCount"></span>
  </div>

  <div class="table-container">
    <table class="team-table" id="empTable">
      <thead>
        <tr>
          <th onclick="sortTable(0)" style="width:30%">
            Nom complet <span class="sort-icon" id="sortIcon0">⇅</span>
          </th>
          <th onclick="sortTable(1)" style="width:25%">
            Service <span class="sort-icon" id="sortIcon1">⇅</span>
          </th>
          <?php if($_SESSION['role'] == 'responsable') { ?>
          <th class="no-sort" style="width:25%">Equipe de maintenance</th>
          <?php } ?>
          <?php if($_SESSION['role'] == 'admin') { ?>
          <th class="no-sort" style="width:35%">Réinitialisation mot de passe</th>
          <?php } ?>
        </tr>
      </thead>
      <tbody id="tableBody">
        <?php
          $sql = "SELECT U.id, U.full_name, U.login, U.team_member, U.mdp, D.name FROM users U, departments D WHERE U.departement = D.id";
          $result = mysqli_query($conn, $sql);
          while($row = mysqli_fetch_assoc($result)) {
        ?>
        <tr data-name="<?php echo htmlspecialchars(strtolower($row['full_name'])); ?>"
            data-dept="<?php echo htmlspecialchars($row['name']); ?>">
          <td><?php echo htmlspecialchars($row['full_name']); ?></td>
          <td><?php echo htmlspecialchars($row['name']); ?></td>

          <?php if($_SESSION['role'] == 'responsable') { ?>
          <td>
            <?php if($row['team_member'] == 0) { ?>
              <a class="btn-ajouter" href="team_emp.php?id_emp=<?php echo $row['id']; ?>">
                ＋ Ajouter
              </a>
            <?php } else { ?>
              <a class="btn-supprimer" href="team_emp.php?id_emp=<?php echo $row['id']; ?>">
                − Supprimer
              </a>
            <?php } ?>
          </td>
          <?php } ?>

          <?php if($_SESSION['role'] == 'admin') { ?>
            <?php if(strlen($row['mdp'].'') > 0) { ?>
          <td>
            <span class="mdp-text"><?php echo htmlspecialchars($row['mdp']); ?></span>
            <a class="btn-reinit" href="reset_password.php?id_emp=<?php echo $row['id']; ?>">
              🔑 Réinitialiser
            </a>
          </td>
            <?php } else { ?>
          <td><span style="color:#9ca3af; font-size:13px;">—</span></td>
            <?php } ?>
          <?php } ?>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<script src="vendors/base/vendor.bundle.base.js"></script>
<script src="js/template.js"></script>

<script>
  const colorPalette = ['#1aa957','#2563eb','#d97706','#7c3aed','#dc2626','#0891b2','#db2777'];
  const deptColors = {};
  let colorIndex = 0;
  let sortCol = -1, sortDir = 1;
  let activeFilter = 'Tous';

  const rows = Array.from(document.querySelectorAll('#tableBody tr'));
  const depts = [...new Set(rows.map(r => r.dataset.dept))];

  depts.forEach(d => {
    deptColors[d] = colorPalette[colorIndex % colorPalette.length];
    colorIndex++;
  });

  function getInitials(name) {
    return name.trim().split(/\s+/).map(w => w[0] || '').join('').toUpperCase().slice(0, 2) || '?';
  }

  // Style rows: avatar + badge
  rows.forEach(row => {
    const nameTd = row.cells[0];
    const deptTd = row.cells[1];
    const name = nameTd.textContent.trim();
    const dept = row.dataset.dept;
    const color = deptColors[dept];

    nameTd.innerHTML = `
      <div class="name-cell">
        <div class="avatar" style="background:${color}22; color:${color}; border:1.5px solid ${color}66">
          ${getInitials(name)}
        </div>
        ${name}
      </div>`;

    deptTd.innerHTML = `
      <span class="dept-badge" style="background:${color}18; color:${color}; border:1px solid ${color}44">
        ${dept}
      </span>`;
  });

  // Build filter buttons
  function buildFilters() {
    const wrap = document.getElementById('filterBtns');
    const all = ['Tous', ...depts];
    wrap.innerHTML = all.map(d =>
      `<button class="filter-btn ${d === activeFilter ? 'active' : ''}" onclick="setFilter('${d.replace(/'/g,"\\'")}')">
        ${d}
      </button>`
    ).join('');
  }

  function setFilter(dept) {
    activeFilter = dept;
    buildFilters();
    applyFilters();
  }

  document.getElementById('searchInput').addEventListener('input', applyFilters);

  function applyFilters() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    let visible = 0;
    rows.forEach(row => {
      const nameMatch = row.dataset.name.includes(q);
      const deptMatch = row.dataset.dept.toLowerCase().includes(q);
      const filterMatch = activeFilter === 'Tous' || row.dataset.dept === activeFilter;
      const show = (nameMatch || deptMatch) && filterMatch;
      row.style.display = show ? '' : 'none';
      if (show) visible++;
    });

    let idx = 0;
    rows.forEach(row => {
      if (row.style.display !== 'none') {
        row.style.background = idx % 2 === 0 ? '#ffffff' : '#f9fafb';
        idx++;
      }
    });

    const tbody = document.getElementById('tableBody');
    const existing = document.getElementById('noResults');
    if (existing) existing.remove();
    if (visible === 0) {
      tbody.insertAdjacentHTML('beforeend',
        '<tr id="noResults"><td colspan="3" class="no-results">Aucun résultat trouvé</td></tr>');
    }

    document.getElementById('resultCount').textContent =
      visible + ' employé' + (visible !== 1 ? 's' : '');
  }

  function sortTable(col) {
    if (sortCol === col) sortDir *= -1;
    else { sortCol = col; sortDir = 1; }

    ['sortIcon0','sortIcon1'].forEach((id, i) => {
      const el = document.getElementById(id);
      if(el) el.textContent = i === col ? (sortDir === 1 ? '▲' : '▼') : '⇅';
    });

    const tbody = document.getElementById('tableBody');
    const sorted = rows.slice().sort((a, b) => {
      const va = col === 0 ? a.dataset.name : a.dataset.dept.toLowerCase();
      const vb = col === 0 ? b.dataset.name : b.dataset.dept.toLowerCase();
      return va.localeCompare(vb, 'fr') * sortDir;
    });
    sorted.forEach(r => tbody.appendChild(r));
    applyFilters();
  }

  buildFilters();
  applyFilters();
</script>

</body>
</html>