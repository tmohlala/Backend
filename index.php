<?php
require_once 'core/init.php';

 if(Session::exists('home')) {
 	echo '<h3 style="color:blue" position="relative">' .Session::flash('home') . '</h3>';
 }

 $user = new User();
 if($user->isLoggedIn()) {
 	?>
 		<p>Hello <a href="profile.php?user=<?php echo escape($user->data()->username); ?>"><?php echo escape($user->data()->username); ?></a>!</p>
 		<ul>
 			<li><a href="logout.php">log out</a></li>
 			<li><a href="update.php">Change username</a></li>
 			<li><a href="changePassword.php">Change password</a></li>
 		</ul>
 	<?php
 	if($user->hasPermission('admin')) {
 		echo '<p>You are an administrator</p>';
 	}
 }
 else {
 	echo '<p>You need to <a href="login.php">log in</a> or <a href="register.php">register</a></p>';
 }