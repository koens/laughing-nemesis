<?PHP
/**
 * Streamers Admin Panel
 *
 * Originally written by Sebastian Graebner <djcrackhome>
 * Fixed and edited by David Schomburg <dave>
 *
 * The Streamers Admin Panel is a web-based administration interface for
 * Nullsoft, Inc.'s SHOUTcast Distributed Network Audio Server (DNAS),
 * and is intended for use on the Linux-distribution Debian.
 *
 * LICENSE: This work is licensed under the Creative Commons Attribution-
 * ShareAlike 3.0 Unported License. To view a copy of this license, visit
 * http://creativecommons.org/licenses/by-sa/3.0/ or send a letter to
 * Creative Commons, 444 Castro Street, Suite 900, Mountain View, California,
 * 94041, USA.
 *
 * @author     Sebastian Graebner <djcrackhome@streamerspanel.com>
 * @author     David Schomburg <dave@streamerspanel.com>
 * @copyright  2009-2012  S. Graebner <djcrackhome> D. Schomburg <dave>
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported License
 * @link       http://www.streamerspanel.com

 */

if (stripos($_SERVER['PHP_SELF'], 'content.php') === false) {
    die ("You can't access this file directly...");
}

$settingsq = mysql_query("SELECT * FROM settings WHERE id='0'") or die($messages["g5"]);
foreach(mysql_fetch_array($settingsq) as $key => $pref) {
	if (!is_numeric($key)) {
		$setting[$key] = stripslashes($pref);
	}
}

if (isset($_POST['submit'])) {
	if (isset($_POST['email'])) {
		if (!strstr($_POST['email'],"@")) {
			$formerror = "email";
		}
		else {
			if (empty($_POST['reason'])) {
				$formerror = "reason";
			}
			else {
				if (empty($_POST['message'])) {
					$formerror = "message";
				}
				else {
					if (function_exists('htmlspecialchars_decode'))
						$messagesql = htmlspecialchars_decode(mysql_real_escape_string($_POST['message']), ENT_QUOTES);
                        //$messagesql = mysql_real_escape_string($messagesql);
					if (function_exists('htmlspecialchars_decode'))
						$reasonsql = htmlspecialchars_decode(mysql_real_escape_string($_POST['reason']), ENT_QUOTES);
                        //$reasonsql = mysql_real_escape_string($reasonsql);
					if (mysql_query("INSERT INTO notices (username,reason,message,ip) VALUES('".$loginun."','".$reasonsql."','".$messagesql."','".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."')")) {
						$correc[] = "<h2>".$messages["355"]."</h2>";
					}
					else {
						$errors[] = "<h2>".$messages["356"]."</h2>";
					}
				}
			}
		}
	}
	else {
		$errors[] = "<h2>".$messages["357"]."</h2>";
		$formerror = "email";
	}
}