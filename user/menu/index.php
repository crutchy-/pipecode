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

print_header("Menu");

writeln('<table class="fill">');
writeln('<tr>');
writeln('<td style="padding-right: 8px; vertical-align: text-top; width: 50%;">');

beg_tab();
print_row(array("caption" => "Feed", "description" => "View news feeds", "icon" => "news", "link" => "/"));
print_row(array("caption" => "Comments", "description" => "View your past comments", "icon" => "chat", "link" => "/comments"));
print_row(array("caption" => "Karma", "description" => "Monitor your karma rating", "icon" => "karma-good", "link" => "/karma/"));
end_tab();

writeln('</td>');
writeln('<td style="vertical-align: text-top; width: 50%;">');

beg_tab();
print_row(array("caption" => "Mail", "description" => "Send and receive mail", "icon" => "mail", "link" => "/mail/"));
print_row(array("caption" => "Settings", "description" => "Configure your account settings", "icon" => "tools", "link" => "/settings"));
end_tab();

writeln('</td>');
writeln('</tr>');
writeln('</table>');

print_footer();
