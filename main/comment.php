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

include("render.php");

$cid = (int) $s2;
if (!string_uses($cid, "[0-9]")) {
	die("invalid cid [$cid]");
}
$comment = db_get_rec("comment", $cid);
$can_moderate = false;
$a = article_info($comment);

print_header($comment["subject"]);

writeln('<table class="fill">');
writeln('<tr>');
writeln('<td class="left_col">');
print_left_bar("main", "stories");
writeln('</td>');
writeln('<td class="fill">');

writeln('<h1>' . ucwords($a["type"]) . '</h1>');
writeln('<a href="' . $a["link"] . '">' . $a["title"] . '</a>');

writeln('<h2>Preview</h2>');
writeln(render_comment($comment["subject"], $comment["zid"], $comment["time"], $comment["cid"], $comment["comment"]));
writeln('</div>');

writeln('</td>');
writeln('</tr>');
writeln('</table>');
