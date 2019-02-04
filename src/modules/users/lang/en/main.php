<?php


	if (!defined("IN-GEKKO")) die("Get a life!");

	Lang::Set("E_LOGIN_FAILED", "Authentication failed. The username may not exist or your password is incorrect. Please verify your account information..");

	Lang::Set("E_TOO_MANY_FAILED_LOGIN_ATTEMPTS", "Too many login attempts were received from your computer. The login page has been temporary disabled for your.");

	Lang::Set("L_MODIFY_PASSWORD", "Modify password");
	Lang::Set("L_NEW_ACCOUNT", "New account");
	Lang::Set("L_ALPHA_CHARS", "You can use letters, numbers and underscore (_), without spaces.");
	Lang::Set("L_CONFIRM_ACCOUNT", "Confirm account");
	Lang::Set("L_RESEND_CONFIRMATION", "Resend confirmation e-mail");

	Lang::Set("L_PROFILE", "Profile");

	Lang::Set("L_A_HOUR", "A hour");
	Lang::Set("L_A_DAY", "A day");
	Lang::Set("L_A_WEEK", "A week");
	Lang::Set("L_A_MONTH", "A month");

	Lang::Set("E_SHORT_PASSWORD", "The password you provided is very short");
	Lang::Set("E_PASSWORD_MISMATCH", "The passwords you entered doesn't match");
	Lang::Set("E_INVALID_USERNAME", "The given username contains invalid characters. You may use only letters, numbers and underscore (_), without spaces");
	Lang::Set("E_INVALID_EMAIL", "The given e-mail address seems incorrect");
	Lang::Set("E_DUPLICATED_USERNAME", "That username is already taken by someone else.");
	Lang::Set("E_DUPLICATED_EMAIL", "That e-mail is already assigned to another account");

	Lang::Set("L_CREATE_ACCOUNT", "Create account");
	Lang::Set("L_ACCOUNT_INFO", "Account info");
	Lang::Set("L_USER_REGISTRATION", "User registration");
	Lang::Set("L_SESSION_LENGTH", "Session length (minutes)");
	Lang::Set("L_USER_GROUPS", "User groups");

	Lang::Set("L_NO_SUCH_PROFILE", "This user has not created a profile.");

	Lang::Set("L_ASCENDENT", "Ascending");
	Lang::Set("L_SORT", "Order");
	Lang::Set("L_DESCENDENT", "Descending");

	Lang::Set("L_ACCOUNT_CONFIRMATION", "Account confirmation");

	Lang::Set("L_ACCOUNT_CONFIRMATION_SENT", "And e-mail has been sent to you, in order to confirm your account please fill the blanks in this form.");

	Lang::Set("L_USER_PANEL", "User Panel");
	Lang::Set("L_USER_SETTINGS", "User preferences");
	Lang::Set("L_MY_PROFILE", "My profile");

	Lang::Set("L_MY_ACCOUNT_HINT", "Here you can change your account preferences such as your username, e-mail, etc. To perform those changes you need your current password. You are also allowed to update your <a href=\"{=URLEVALPROTOTYPE(\"index.php/module=users/action=profile\")=}\">profile</a>");

	Lang::Set("L_CURRENT_PASSWORD", "Current password");
	Lang::Set("L_NEW_PASSWORD", "New password");
	Lang::Set("L_CHANGE_PASSWORD", "Change password");

	Lang::Set("L_MY_PROFILE_HINT", "The profile is the information you want to share with the website's visitors.<br />
<b>Links:</b> <a href=\"{=URLEVALPROTOTYPE(\"index.php/module=users/action=account\")=}\">User preferences</a>");

	Lang::Set("L_AVATAR", "Avatar");
	Lang::Set("L_BIRTHDATE", "Birthday (day/month/year)");
	Lang::Set("L_ABOUT_ME", "About me;");
	Lang::Set("L_LOCATION", "Location");
	Lang::Set("L_GENDER", "Gender");
	Lang::Set("L_MSN", "MSN Messenger ID");
	Lang::Set("L_YIM", "Yahoo Messenger ID");
	Lang::Set("L_ICQ", "ICQ");
	Lang::Set("L_SIGNATURE", "Personal signature");
	Lang::Set("L_PUBLIC_EMAIL", "Email address (Public)");
	Lang::Set("L_WEBSITE", "Personal website");
	Lang::Set("L_AGE", "Age");
	Lang::Set("L_YEARS", "Years");
	Lang::Set("L_USER_PROFILE", "User profile");
	Lang::Set("L_EDIT_PROFILE", "Edit Profile");
	Lang::Set("L_MALE", "Male");
	Lang::Set("L_FEMALE", "Female");
	Lang::Set("L_LOGIN", "Login");
	Lang::Set("L_REALNAME", "Real name");
	Lang::Set("L_WELCOME", "Welcome");
	Lang::Set("L_LAST_LOGIN", "last login");
	Lang::Set("L_CREATE_USER", "Create user");

	Lang::Set("L_MUST_CHANGE_PASSWORD_TOO", "The password must be changed aswell");

	Lang::Set("L_TRIGGER_GROUPS", "Show/Hide groups &raquo;");
	Lang::Set("L_CONFIRM_PASSWORD", "Confirm password");

	Lang::Set("L_CONTACT_INFO", "Contact information");

	Lang::Set("L_CONFIRMATION_CODE", "Account confirmation code");
	Lang::Set("L_RESET_PASSWORD", "Reset password");
	Lang::Set("L_GROUP_USERS", "Users: They have the right about the creation modification and deletion of the users.");
	Lang::Set("L_GROUP_NORMAL_USER", "Normal users: Default group for new members.");
	Lang::Set("L_GROUP_ANONYMOUS", "Anonymous: Visitors.");
?>