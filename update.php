<?php

/**
* Functionality for updating user profile
* information.
*/

require_once 'core/init.php';

$user = new User();

if(!$user->isLoggedIn()) {
	Redirect::to('index.php');
}

if(Input::exists()) {
	if(Token::check(Input::get('token'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, [
			'username' => [
				'required' => true,
				'min' => 2,
				'max' => 50
			]
		]);

		if($validation->passed()) {
			try {
				$user->update([
					'username' => Input::get('username'),
				]);
				Session::flash('home', 'Profile update succesful!');
				Redirect::to('index.php');
			} catch(Exception $e) {
				die($e->getMessage());
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
		<label for="username">Name</label>
		<input type="text" name="username" value="<?php echo escape($user->data()->username); ?>">

		<input type="submit" value="update">

		<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
	</div>
</form>