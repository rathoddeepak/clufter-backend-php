<!DOCTYPE html>
<html lang="en">
<head>
	<title>Clufter</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="cs/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="cs/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="cs/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="cs/vendor/animate/animate.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="cs/vendor/select2/select2.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="cs/css/util.css">
	<link rel="stylesheet" type="text/css" href="cs/css/main.css">
<!--===============================================================================================-->
</head>
<body>
	
	
	<div class="bg-g1 size1 flex-w flex-col-c-sb p-l-15 p-r-15 p-b-30" style="justify-content:center">		

		<div class="flex-col-c w-full p-t-50 p-b-80">
			<?php
			 require_once('server/main.php');
			 $name = empty($_GET['name']) ? null : $_GET['name'];
			 $phone = empty($_GET['id']) ? null : $_GET['id'];
			 global $db;
			 if($phone == null ||  $name == null){
			 	$insert = false;
			 }else{
			 	$insert = $db->insert("subscribe", [
				 	'name' => $name,
				 	'mail_phone' => $phone,
				 ]);
			 }
			?>
			<h3 class="l1-txt1 txt-center p-b-10">				
				<?php if($insert) { ?>
				Subscribed Sucessfully
				<?php } else { ?>
				Coming Soon	
				<?php } ?>
			</h3>

			<p class="txt-center l1-txt2 p-b-43 wsize2">
				Our website CLUFTER is under construction, follow us for update now!
			</p>

			<form class="flex-w flex-c-m w-full contact100-form validate-form">
				<div class="wrap-input100 validate-input where1" data-validate = "Name is required">
					<input class="s1-txt3 placeholder0 input100" type="text" id="name" name="name" placeholder="Name">
				</div>

				<div class="wrap-input100 validate-input where1" data-validate = "Mail or Phone number required: ex@abc.xyz">
					<input class="s1-txt3 placeholder0 input100" type="text" name="id" name="email" placeholder="Mail or Phone number">
				</div>

				<button class="flex-c-m s1-txt4 size3 how-btn trans-04 where1" id="btn" onclick="update()">
					Get Updates
				</button>
				
			</form>			
		</div>

	</div>



	

<!--===============================================================================================-->	
	<script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/bootstrap/js/popper.js"></script>
	<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->	
<!--===============================================================================================-->
	<script src="vendor/tilt/tilt.jquery.min.js"></script>
	<script >
		$('.js-tilt').tilt({
			scale: 1.1
		})
	</script>
<!--===============================================================================================-->
	

</body>
</html>