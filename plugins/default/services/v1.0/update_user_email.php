<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Haden Hiles
 * @link      https://github.com/HadenHiles
 */

 $guid       = input('guid');
 $email      = input('new_email');

 if($guid) {
 		$user = ossn_user_by_guid($guid);
 }
 $currentUser = ossn_loggedin_user();
 if($user && $user->guid == $currentUser->guid) {
 		$OssnUser           = new OssnUser;

 		if(empty($email)) {
 				$params['OssnServices']->throwError('103', 'No email provided');
 		}
 		if($user->email !== $email) {
 				if($OssnUser->isOssnEmail()) {
 						$params['OssnServices']->throwError('103', ossn_print('ossnservices:emailalreadyinuse'));
 				}
 		}

 		$OssnDatabase     = new OssnDatabase;
 		$params['table']  = 'ossn_users';
 		$params['wheres'] = array(
 				"guid='{$user->guid}'"
 		);

 		$params['names']  = array(
 				'email'
 		);
 		$params['values'] = array(
 				$email
 		);

 		if($OssnDatabase->update($params)) {
				$user->save();
 				$user = $params['OssnServices']->setUser(ossn_user_by_guid($user->guid)); //get user again with new contents
 				$params['OssnServices']->successResponse($user);
 		}
 } else {
   $component = new OssnComponents;
   $settings  = $component->getSettings('OAuthLogin');
   if (!empty($settings)) {
     header('Location: ' . $settings->wp_consumer_authorization_url . '?response_type=code&client_id=' . $settings->wp_consumer_key . '&redirect_uri=' . ossn_site_url('oauth_login/wordpress'));
   }
 }
 $params['OssnServices']->throwError('103', ossn_print('ossnservices:usereditfailed'));
