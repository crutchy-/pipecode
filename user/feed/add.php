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

include("feed.php");

if ($zid != $auth_zid) {
	die("not your page");
}

$col = http_get_int("col");
if ($col < 0 || $col > 2) {
	die("invalid col [$col]");
}

if (http_post()) {
	$fid = http_post_int("fid", array("required" => false));
	$uri = http_post_string("uri", array("required" => false, "len" => 100, "valid" => "[a-z][A-Z][0-9]~@#$%&()-_=+[];:,./?"));

	if ($fid == 0) {
		if ($uri == "") {
			die("no feed uri given");
		}
		$fid = add_feed($uri);
	}
	if (!db_has_rec("feed", $fid)) {
		die("fid not found [$fid]");
	}
	if (db_has_rec("feed_user", array("zid" => $auth_zid, "fid" => $fid))) {
		die("feed [$fid] is already on your page");
	}

	$row = run_sql("select max(pos) as max_pos from feed_user where zid = ? and col = ?", array($auth_zid, $col));
	$pos = $row[0]["max_pos"] + 1;

	$feed_user = array();
	$feed_user["zid"] = $auth_zid;
	$feed_user["fid"] = $fid;
	$feed_user["col"] = $col;
	$feed_user["pos"] = $pos;
	db_set_rec("feed_user", $feed_user);
	header("Location: edit");
	die();
}

print_header();

writeln('<table class="fill">');
writeln('<tr>');
writeln('<td class="left_col">');
print_left_bar("account", "feed");
writeln('</td>');
writeln('<td class="fill">');

writeln('<form method="post">');
writeln('<div class="dialog_title">Add Feed</div>');
writeln('<div class="dialog_body">');

writeln('<table style="width: 100%">');
writeln('	<tr>');
writeln('		<td style="width: 120px">Use existing feed:</td>');
writeln('		<td>');
writeln('			<select name="fid" style="width: 100%">');
writeln('				<option value="0">(select feed)</option>');

$existing = array();
$row = run_sql("select fid from feed_user where zid = ?", array($auth_zid));
for ($i = 0; $i < count($row); $i++) {
	$existing[$row[$i]["fid"]] = 1;
}

$row = run_sql("select fid, title from feed order by title");
for ($i = 0; $i < count($row); $i++) {
	if (!array_key_exists($row[$i]["fid"], $existing)) {
		writeln('				<option value="' . $row[$i]["fid"] . '">' . $row[$i]["title"] . '</option>');
	}
}
writeln('			</select>');
writeln('		</td>');
writeln('	</tr>');
writeln('	<tr>');
writeln('		<td style="width: 120px">Or add new feed:</td>');
writeln('		<td><input name="uri" type="text" style="width: 100%"/></td>');
writeln('	</tr>');
writeln('	<tr>');
writeln('		<td colspan="2" class="right"><input type="submit" value="Add"/></td>');
writeln('	</tr>');
writeln('</table>');

writeln('</div>');
writeln('</form>');

writeln('</td>');
writeln('</tr>');
writeln('</table>');

print_footer();
