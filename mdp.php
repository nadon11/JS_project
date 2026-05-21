<?php 
	include("dbase.php");
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$mdp=filter_input(INPUT_POST , "mdp" , FILTER_SANITIZE_SPECIAL_CHARS);
		$code=filter_input(INPUT_POST , "mdp" , FILTER_SANITIZE_SPECIAL_CHARS);
		$error='';
		$right='';
		if(empty($mdp)){
      $error= 'Veuillez saisir le nom d utilisateur';
			}
		else{
			$sql="SELECT * FROM users WHERE login = '$mdp'";
			$result=mysqli_query($conn,$sql);
			if(mysqli_num_rows($result) > 0){
				$row=mysqli_fetch_assoc($result);
				$sql="UPDATE users SET mdp = '".date('Y-m-d H:i:s')."' WHERE login = '$mdp' ";
				if(mysqli_query($conn,$sql)){
          $right='Données enregistrées avec succés';
				}
			}
			else{
        $error='Nom d utilisateur introuvable';
			}					
		}
	}
?>
<!DOCTYPE html>
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Alecso</title>
  <!-- base:css -->
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/base/vendor.bundle.base.css">
  <!-- endinject -->
  <!-- plugin css for this page -->
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="css/style.css">
  <!-- endinject -->
  <link rel="shortcut icon" href="images/logo_alec.jpg">
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth lock-full-bg">
        <div class="row w-100">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form text-left p-5 text-center" style="background: #ffffffa6;">
	
              <form class="pt-5" action="<?php htmlspecialchars($_SERVER["PHP_SELF"])?>" method="post">
                <div class="form-group">
                  <label for="examplePassword1" style="font-size: 26px; margin-top: -39px;">Saisissez le nom d'utilisateur</label>
				  <?php if(isset($error) && strlen($error)) echo '<p  color: #de1d34;"><i class="mdi mdi-alert-outline"> '.$error.'</i></p>';?>	
					<?php if(isset($right) && strlen($right)) echo '<p  color: green;"><i class="mdi mdi-check-outline"> '.$right.'</i></p>';?>
                  <input type="text" name= "mdp" class="form-control form-control-lg border-left-0" id="exampleInputEmail" style="border: 2px solid #878282; font-size: 17px;" placeholder="Nom d'utilisateur" value="<?php echo isset($_POST['mdp'])?$_POST['mdp']:'';?>">
                </div>
                <div class="mt-5">
                  <button type="submit" name="submit" class="btn btn-block btn-success btn-lg font-weight-medium">Confirmer</button>
				
                </div>
          <a  class="btn btn-link btn-fw" href="login.php">Retour</a> 
				</form>
            </div>
          </div>
        </div>
      </div>
      <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
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