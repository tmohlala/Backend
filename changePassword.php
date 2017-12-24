<?php
require_once 'core/init.php'; 

$user = new User();

if(!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

if(Input::exists()) {
	if(Token::check(Input::get('token'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST,[
			'password_current' => [
				'required' => true,
				'min' => 6
			],
			'password_new' => [
				'required' => true,
				'min' => 6
			],
			'password_new_again' => [
				'required' => true,
				'min' => 6,
				'matches' => 'password_new'
			]
		]);

		if($validation->passed()) {
			$password_current = Hash::make(Input::get('password_current'), $user->data()->salt);
			if($password_current !== $user->data()->password) {
				echo "You current password is wrong!";
			}
			else {
				if($password_current === Hash::make(Input::get('password_new'), $user->data()->salt)) {
					echo 'New password is similar to old password!';
				}
				else {
					$salt = Hash::salt(32);
					$user->update([
						'password' => Hash::make(Input::get('password_new'), $salt),
						'salt' => $salt
					]);
				}
				Session::flash('home', 'Password change succesfully.');
				Redirect::to('index.php');
			}
		}
		else {
			foreach($validation->errors() as $error) {
				echo '<p style="color:red">' . $error . '</p>';
			}
		}
	}
}
?>

<form action="" method="post">
	<div class="field">
		<label for="password_current">Current password</label>
		<input type="password" name="password_current" id="password_current">
	</div>

	<div class="field">
		<label for="password_new">New password</label>
		<input type="password" name="password_new" id="password_new">
	</div>

	<div class="field">
		<label for="password_new_again">Re-type new password</label>
		<input type="password" name="password_new_again" id="password_new_again">
	</div>

	<input type="submit" value="Change">
	<input type="hidden" name="token" value="<?php echo Token::generate(); ?>" >
</form>