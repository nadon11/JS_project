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
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Kapella Bootstrap Admin Dashboard Template</title>
  <!-- base:css -->
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/favicon.png" />
</head>

<body>	
<div class="horizontal-menu">
      <nav class="navbar top-navbar col-lg-12 col-12 p-0">
        <div class="container-fluid">
          <div class="navbar-menu-wrapper d-flex align-items-center justify-content-between">
            <ul class="navbar-nav navbar-nav-left">
              <li class="nav-item ms-0 me-5 d-lg-flex d-none">
                <a href="#" class="nav-link horizontal-nav-left-menu"><i class="mdi mdi-format-list-bulleted"></i></a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle d-flex align-items-center justify-content-center" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
                  <i class="mdi mdi-bell mx-0"></i>
                  <span class="count bg-success">2</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                  <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-success">
                          <i class="mdi mdi-information mx-0"></i>
                        </div>
                    </div>
                    <div class="preview-item-content">
                        <h6 class="preview-subject font-weight-normal">Application Error</h6>
                        <p class="font-weight-light small-text mb-0 text-muted">
                          Just now
                        </p>
                    </div>
                  </a>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-warning">
                          <i class="mdi mdi-settings mx-0"></i>
                        </div>
                    </div>
                    <div class="preview-item-content">
                        <h6 class="preview-subject font-weight-normal">Settings</h6>
                        <p class="font-weight-light small-text mb-0 text-muted">
                          Private message
                        </p>
                    </div>
                  </a>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                        <div class="preview-icon bg-info">
                          <i class="mdi mdi-account-box mx-0"></i>
                        </div>
                    </div>
                    <div class="preview-item-content">
                        <h6 class="preview-subject font-weight-normal">New user registration</h6>
                        <p class="font-weight-light small-text mb-0 text-muted">
                          2 days ago
                        </p>
                    </div>
                  </a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link count-indicator dropdown-toggle d-flex justify-content-center align-items-center" id="messageDropdown" href="#" data-bs-toggle="dropdown">
                  <i class="mdi mdi-email mx-0"></i>
                  <span class="count bg-primary">4</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
                  <p class="mb-0 font-weight-normal float-left dropdown-header">Messages</p>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                        <img src="images/faces/face4.jpg" alt="image" class="profile-pic">
                    </div>
                    <div class="preview-item-content flex-grow">
                        <h6 class="preview-subject ellipsis font-weight-normal">David Grey
                        </h6>
                        <p class="font-weight-light small-text text-muted mb-0">
                          The meeting is cancelled
                        </p>
                    </div>
                  </a>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                        <img src="images/faces/face2.jpg" alt="image" class="profile-pic">
                    </div>
                    <div class="preview-item-content flex-grow">
                        <h6 class="preview-subject ellipsis font-weight-normal">Tim Cook
                        </h6>
                        <p class="font-weight-light small-text text-muted mb-0">
                          New product launch
                        </p>
                    </div>
                  </a>
                  <a class="dropdown-item preview-item">
                    <div class="preview-thumbnail">
                        <img src="images/faces/face3.jpg" alt="image" class="profile-pic">
                    </div>
                    <div class="preview-item-content flex-grow">
                        <h6 class="preview-subject ellipsis font-weight-normal"> Johnson
                        </h6>
                        <p class="font-weight-light small-text text-muted mb-0">
                          Upcoming board meeting
                        </p>
                    </div>
                  </a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a href="#" class="nav-link count-indicator "><i class="mdi mdi-message-reply-text"></i></a>
              </li>
              <li class="nav-item nav-search d-none d-lg-block ms-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text" id="search">
                        <i class="mdi mdi-magnify"></i>
                      </span>
                    </div>
                    <input type="text" class="form-control" placeholder="search" aria-label="search" aria-describedby="search">
                </div>
              </li>	
            </ul>
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo" href="index.html"><img src="http://localhost/reclamation/images/auth/logo_alec.jpg" alt="logo" style="width:200px;
				height: 78px"></a>
                <a class="navbar-brand brand-logo-mini" href="index.html"><img src="http://localhost/reclamation/images/auth/logo_alec.jpg" alt="logo" style="width:200px;
				height: 78px"></a>
            </div>
<ul class="navbar-nav navbar-nav-right">
                <li class="nav-item dropdown  d-lg-flex d-none">
                  <button type="button" class="btn btn-inverse-primary btn-sm" style="background-color:#23894e ; color: white" >Product </button>
                </li>
                <li class="nav-item dropdown d-lg-flex d-none">
                  <a class="dropdown-toggle show-dropdown-arrow btn btn-inverse-primary btn-sm" id="nreportDropdown" href="#" data-bs-toggle="dropdown" style="background-color:#23894e ; color: white">
                  Reports
                  </a>
                  <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="nreportDropdown">
                      <p class="mb-0 font-weight-medium float-left dropdown-header" >Reports</p>
                      <a class="dropdown-item">
                        <i class="mdi mdi-file-pdf text-primary"></i>
                        Pdf
                      </a>
                      <a class="dropdown-item">
                        <i class="mdi mdi-file-excel text-primary"></i>
                        Exel
                      </a>
                  </div>
                </li>
                <li class="nav-item dropdown d-lg-flex d-none">
                  <button type="button" class="btn btn-inverse-primary btn-sm" style="background-color:#23894e ; color: white">Settings</button>
                </li>
                <li class="nav-item nav-profile dropdown">
                  <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" id="profileDropdown" >
                    <span class="nav-profile-name" ><?php 
					echo $_SESSION['full_name']; ?></span>
                    <span class="online-status"></span>
                  </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown" data-bs-popper="none">
                      <a class="dropdown-item">
                        <i class="mdi mdi-settings text-primary" style="color:  #1aa957 !important">
					
						</i>
                        Settings
                      </a>
                      <a class="dropdown-item" href="deconnexion.php">
                        <i class="mdi mdi-logout text-primary" style="color:  #1aa957 !important">
						</i>
                        Logout
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
                  <span class="menu-title">Dashboard</span>
                </a>
              </li>
             
              <li class="nav-item">
                  <a href="teams.php" class="nav-link">
                    <i class="mdi mdi-wrench menu-icon"></i>
                    <span class="menu-title">Teams</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
            
              
              <li class="nav-item">
                  <a href="tab_employee.php" class="nav-link">
                    <i class="mdi mdi-emoticon menu-icon"></i>
                    <span class="menu-title">Employee</span>
                    <i class="menu-arrow"></i>
                  </a>
              </li>
              <li class="nav-item">
                  <a href="#" class="nav-link">
                    <i class="mdi mdi-codepen menu-icon"></i>
                    <span class="menu-title">Sample Pages</span>
                    <i class="menu-arrow"></i>
                  </a>
                  <div class="submenu">
                      <ul class="submenu-item">
                          <li class="nav-item"><a class="nav-link" href="http://localhost/reclamation/register-2.php">Register</a></li>
                          
                          <li class="nav-item"><a class="nav-link" href="http://localhost/reclamation/login.php">Login</a></li>
                          
                          <li class="nav-item"><a class="nav-link" href="pages/samples/lock-screen.html">Lockscreen</a></li>
                      </ul>
                  </div>
              </li>
              <li class="nav-item">
                  <a href="docs/documentation.html" class="nav-link">
                    <i class="mdi mdi-file-document-box-outline menu-icon"></i>
                    <span class="menu-title">Documentation</span></a>
              </li>
            </ul>
        </div>
      </nav>
    </div>
	<div class="container-fluid page-body-wrapper">
		<div class="main-panel">
			<div class="content-wrapper">
				<div class="row">
					<div class="col-sm-6 mb-4 mb-xl-0">
							<div class="d-lg-flex align-items-center">
								<div>
									<h3 class="text-dark font-weight-bold mb-2">Hi, welcome back!</h3>
									<h6 class="font-weight-normal mb-2">Last login was 23 hours ago. View details</h6>
								</div>
								<div class="ms-lg-5 d-lg-flex d-none">
										<button type="button" class="btn bg-white btn-icon">
											<i class="mdi mdi-view-grid text-success"></i>
									</button>
										<button type="button" class="btn bg-white btn-icon ms-2">
											<i class="mdi mdi-format-list-bulleted font-weight-bold text-primary"></i>
										</button>
								</div>
							</div>
						</div>
				</div>
			<div class="row" style="display: block">
				<div class="col-lg-3" style="margin: auto">
					<h4 class="card-title" style="margin-left: 95px">Claim Statistics</h4>
						<div class="row">
					
						<div class="col-sm-12" ">
							<div class="progress progress-lg grouped mb-2">
								<div class="progress-bar  bg-danger" role="progressbar" style="width: 40%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
								<div class="progress-bar bg-info" role="progressbar" style="width: 10%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
								<div class="progress-bar bg-warning" role="progressbar" style="width: 20%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
								<div class="progress-bar bg-success" role="progressbar" style="width: 30%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
						<div class="col-sm-12">
							<ul class="graphl-legend-rectangle">
								<li style="margin-left: -10px"><span class="bg-info"></span>New claims(15%)</li>
								<li style="margin-left: -10px"><span class="bg-danger"></span>Pending(20%)</li>
								<li style="margin-left: 220px ; margin-top: -50px"><span class="bg-warning"></span>In progress(25%)</li>
								<li style="margin-left: 220px"><span class="bg-success"></span>Completed(40%)</li>
							</ul>
						</div>
					</div>
				</div>
					
			</div>
		
	<div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Complaint table</h4>
                  <p class="card-description">
                    Add class <code>.table-striped</code>
                  </p>
                  <div class="table-responsive">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          
                          <th>
                            Full name
                          </th>
						  <th>
						    Damage
						  </th>
                          <th>
                            Case
                          </th>
                          
                          <th>
                            Date of complaint
                          </th>
						  <th>
							type
							</th>
						  <th>
						    Teams
						  </th>
                        </tr>
                      </thead>
                      <tbody>
					  <?php 
						$sql="SELECT C.* ,U.full_name, U.id as id_user, U.departement, T.type, A.id_employee FROM claim C, users U, type_reclamation T, affect_rec A WHERE C.user_id = U.id AND C.id_type=T.id  AND C.id = A.id_claim AND  A.id_employee = ".$_SESSION['user_id'];
						$result=mysqli_query($conn,$sql);
						while($row=mysqli_fetch_assoc($result)){
						?>
                        <tr>
						 <td><?php echo $row['full_name'] ?></td>
						 <td><?php if($row['damage'] == "little" ){ ?>
							 <button type="button" class="btn btn-warning btn-rounded btn-fw">Little</button>
							<?php
						 }
						 elseif($row['damage'] == "very little" ) { ?>
							 <button type="button" class="btn btn-success btn-rounded btn-fw">Very little</button>
                          <?php 
						 }
						 else{ ?>
						 <button type="button" class="btn btn-danger btn-rounded btn-fw">Extreme</button>
						 <?php
						 } ?>
						 </td>
						 <td>
						 <?php if($row['status'] == "pending"){ ?>
							<label class="badge badge-danger"  >Pending</label>
							<?php
								}
								elseif($row['status'] == "Completed"){
									?>
								 <label class="badge badge-success"  >Completed</label>
							<?php
								}
								else{
									?>
								 <label class="badge badge-warning"  >In progress</label>
							<?php
								}
							?>
							</td>
							<td><?php echo $row['date_add'] ?></td>
						  <td><?php echo $row['type'] ?></td>
						  <td><a  class="btn btn-inverse-success btn-fw" href="edit_rec.php?id_rec=<?php echo $row['id'];?>">Edit</a>
                        </tr>
						  <?php 
						}
						?>
                          
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
		
		</div>
		</div>
	</div>
	<!-- container-scroller -->
  <!-- base:js -->
  <script src="vendors/base/vendor.bundle.base.js"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="js/template.js"></script>
  <!-- endinject -->
</body>
</html>