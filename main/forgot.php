<?
//
// Pipecode - distributed social network
// Copyright (C) 2014 Bryan Beicker <bryan@pipedot.org>
//
// This file is part of Pipecode.
//
// Pipecode is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Pipecode is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Pipecode.  If not, see <http://www.gnu.org/licenses/>.
//

include("mail.php");

$verify = http_get_string("verify", array("required" => false, "len" => 64, "valid" => "[0-9]abcdef"));
if (strlen($verify) != 0 && strlen($verify) != 64) {
	die("invalid verify hash");
}
if ($verify != "") {
	$email_challenge = db_get_rec("email_challenge", array("challenge" => $verify));
	$zid = strtolower($email_challenge["username"]) . "@$site_name";
	if (!is_local_user($zid)) {
		die("no such user [$zid]");
	}
	$user_conf = db_get_conf("user_conf", $zid);
}

if (http_post()) {
	if ($verify != "") {
		$password_1 = http_post_string("password_1", array("len" => 64, "valid" => "[KEYBOARD]"));
		$password_2 = http_post_string("password_2", array("len" => 64, "valid" => "[KEYBOARD]"));

		if (strlen($password_1) < 6) {
			die("password too short");
		}
		if ($password_1 != $password_2) {
			die("passwords do not match");
		}

		$salt = crypt_sha256(rand());
		$password = crypt_sha256("$password_1$salt");

		$user_conf["password"] = $password;
		$user_conf["salt"] = $salt;
		db_set_conf("user_conf", $user_conf, $zid);

		db_del_rec("email_challenge", $verify);

		print_header("Password Reset");
		writeln('<h1>Password Reset</h1>');
		writeln('<p>Don\'t forget it this time!</p>');
		print_footer();
		die();
	}
	$username = http_post_string("username", array("len" => 20, "valid" => "[a-z][A-Z][0-9]"));

	$zid = strtolower($username) . "@$site_name";
	if (!is_local_user($zid)) {
		die("no such user [$zid]");
	}
	$user_conf = db_get_conf("user_conf", $zid);

	$hash = crypt_sha256(rand());

	if (db_has_rec("email_challenge", array("username" => $username))) {
		db_del_rec("email_challenge", array("username" => $username));
	}

	$email_challenge = array();
	$email_challenge["challenge"] = $hash;
	$email_challenge["username"] = $username;
	$email_challenge["email"] = $user["email"];
	$email_challenge["expires"] = time() + 86400 * 3;
	db_set_rec("email_challenge", $email_challenge);

	$subject = "Forgot Password";
	$body = "Did you forget your password for \"$username\" on $server_name?\n";
	$body .= "\n";
	$body .= "In order to reset your password, you must visit the following link:\n";
	$body .= "\n";
	if ($https_enabled) {
		$body .= "https://$server_name/forgot?verify=$hash\n";
	} else {
		$body .= "http://$server_name/forgot?verify=$hash\n";
	}
	$body .= "\n";
	$body .= "This confirmation code will expire in 3 days.\n";

	print_header("Email Sent");
	writeln('<h1>Email Sent</h1>');
	writeln('<p>Please visit the link in the email within 3 days to reset your password.</p>');
	print_footer();

	send_mail($user_conf["email"], $subject, $body);
	die();
}

if ($verify != "") {
	print_header("Reset Password");
	writeln('<h1>Reset Password</h1>');
	if ($https_enabled) {
		writeln('<form action="https://' . $server_name . '/forgot?verify=' . $verify . '" method="post">');
	} else {
		writeln('<form action="/forgot?verify=' . $verify . '" method="post">');
	}
	writeln('<table>');
	writeln('	<tr>');
	writeln('		<td colspan="2">Please choose a new password.</td>');
	writeln('	</tr>');
	writeln('	<tr>');
	writeln('		<td style="padding-top: 8px; text-align: right">Password</td>');
	writeln('		<td style="padding-top: 8px"><input name="password_1" type="password"/></td>');
	writeln('	</tr>');
	writeln('	<tr>');
	writeln('		<td style="padding-bottom: 8px; text-align: right">Password (again)</td>');
	writeln('		<td style="padding-bottom: 8px"><input name="password_2" type="password"/></td>');
	writeln('	</tr>');
	writeln('</table>');
	writeln('<input type="submit" value="Finish"/>');
	writeln('</form>');
	print_footer();

	die();
}

print_header("Forgot Password");

writeln('<h1>Forget Password?</h1>');

writeln('<form method="post">');
writeln('<table>');
writeln('	<tr>');
writeln('		<td>Username</td>');
writeln('		<td><input name="username" type="text"/></td>');
writeln('	</tr>');
writeln('</table>');
writeln('<input type="submit" value="Send"/>');
writeln('</form>');

print_footer();

