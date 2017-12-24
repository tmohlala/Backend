<?php
require_once 'core/init.php';

if(Input::exists()) {
	if(Token::check(Input::get('token'))) {
		$validate = new Validate();
		$validation = $validate->check($_POST, [
			'username' => [
				'required' => true,
				'min' => 2,
				'max' => 20,
				'unique' => 'users'
			],
			'password' => [
				'required' => true,
				'min' => 6,
			],
			'password_again' => [
				'required' => true,
				'matches' => 'password'
			],
			'name' => [
				'required' => true,
				'min' => 2,
				'max' =>  50,
			],
			'email_address' => [
				'required' => true,
				'valid' => true,
				'unique' => 'users'
			]

		]);

		if($validation->passed()) {
			$user = new User();
			$salt = Hash::salt(32);

			try {
				$user->create([
					'username' => Input::get('username'),
					'password' => Hash::make(Input::get('password'), $salt),
					'salt' => $salt,
					'name' => Input::get('name'),
					'joined' => date('Y-m-d H:i:s'),
					'group' => 1,
					'email_address' => Input::get('email_address')
				]);
				Session::flash('home', 'Registration successful and you can now login in!');
				Redirect::to('index.php');
			} catch(Exception $e) {
				echo $e->getMessage();
				Redirect::to('register.php');
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
<form action="" method="post" >
	<div class="field">
		<label for="email_address">email</label>
		<input type="email_address" name="email_address" id="email_address" value="<? echo Input::get('email_address'); ?>">
	</div>

	<div class="field">
		<label for="username">Username</label>
		<input type="text" name="username" id="username" value="<? echo escape(Input::get('username')); ?>" autocomplete="off">
	</div>

	<div class="field">
		<label for="password">Choose password</label>
		<input type="password" name="password" id="password">
	</div>

	<div class="field">
		<label for="password_again">Re-type password</label>
		<input type="password" name="password_again" id="password_again">
	</div>

	<div class="field">
		<label for="name">name</label>
		<input type="text" name="name" id="name" value="<? echo escape(Input::get('name')); ?>">
	</div>

	<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
	<input type="submit" value="register">

</form>