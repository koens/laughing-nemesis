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

function shoutcastcheck($host, $port2, $wait_sec) {
	$fp = @fsockopen($host, $port2, $errstr, $errno, $wait_sec);
	if ($fp) {
		fputs($fp, "GET / HTTP/1.0\r\nUser-Agent:AmIoffOrOn\r\n\r\n");
		$ret = fgets($fp, 255);
		fclose($fp);
		return strpos($ret, '200') !== false;
	}
	else {
		return false;
	}
}
$limit = $setting['display_limit'];
if (!isset($_GET['p'])) {
	$p = 0;
}
else {
	$p = $_GET['p'] * $limit;
}
$l = $p + $limit;
$select = mysql_query("SELECT * FROM servers WHERE sitepublic='1' ORDER BY id ASC LIMIT $p,$limit");
?>
		<h2><?php echo $messages["422"];?></h2>
			<div class="contact_top_menu">
				<div class="tool_top_menu">
					<div class="main_shorttool"><?php echo $messages["423"];?></div>
					<div class="main_righttool">
						<h2><?php echo $messages["424"];?></h2>
						<p><?php echo $messages["425"];?></p>
						<p>&nbsp;</p>
					</div>
				</div>
				<table cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th width="19%"><?php echo $messages["426"];?></th>
							<th width="11%"><?php echo $messages["427"];?></th>
							<th width="28%"><?php echo $messages["428"];?></th>
							<th width="30%"><?php echo $messages["429"];?></th>
							<th width="12%">&nbsp;</th>
						</tr>
					</thead>
				<tbody>
				<?php
				if (mysql_num_rows($select)==0) {
					echo "<tr>
						<td colspan=\"5\">".$messages["430"]."</td>
						</tr>";
				}
				else {
					while($data = mysql_fetch_array($select)) {
						echo '<tr>
							<td><a href="http://'.$setting['host_add'].':'.$data['portbase'].'/" target="_blank">'.$setting['host_add'].'</a></td>
							<td><a href="http://'.$setting['host_add'].':'.$data['portbase'].'/" target="_blank">'.$data['portbase'].'</a></td>
							<td><a href="http://'.$setting['host_add'].':'.$data['portbase'].'/" target="_blank">'.$data['owner'].'</a></td>
							<td><div class="space_show" style="background-position:';
						if (file_exists("./uploads/".$data['portbase']."/")) {
							$dir = "./uploads/".$data['portbase']."/";
							$filesize = 0;
							if(is_dir($dir)) {
								if($dp = opendir($dir)) {
									while( $filename = readdir($dp) ) {
										if(($filename == '.') || ($filename == '..'))
											continue;
										$filedata = stat($dir."/".$filename);
										$filesize += $filedata[7];
									}
									$actual_dir_size = $filesize/1024;
								}
							}
						}
						$negative_background_pos = ($actual_dir_size/$data['webspace'])*130;
						echo '-'.$negative_background_pos.'px 0px;"></div></td><td>';
						if (shoutcastcheck("".$setting['host_add']."", "".$data['portbase']."", 1)) {
							echo "<a class=\"selector\" href=\"http://".$setting['host_add'].":".$data['portbase']."/listen.pls\">".$messages["431"]."</a>";
						}
						else {
							echo "<a class=\"delete\" href=\"#\">".$messages["432"]."</a>";
						}
						echo '</td></tr>';
					}
				}
				?>
				</tbody>
			</table>
			<ul class="paginator">
				<?php
				if (mysql_num_rows($select)==0) {
					// just to show what is ment
				}
				else {
					$page = mysql_num_rows(mysql_query("SELECT * FROM servers WHERE sitepublic='1'"));
					$i = 0;
					$page = mysql_num_rows(mysql_query("SELECT * FROM servers"));
					while($page > "0") {
						echo "<li><a href=\"content.php?include=public&p=";
						if (($p / $limit) == $i){
							echo "";
						}
						echo "$i\">$i</a></li>";
						$i++;
						$page -= $limit;
					}
				}
				?>
			</ul>
		</div>