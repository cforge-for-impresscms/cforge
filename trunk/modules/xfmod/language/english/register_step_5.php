<?php
global $type;

if ($type == "community") { $project = " or project"; }


define("_XF_STEP5","
<p>
In addition to full $type name, you will also need to choose a \"Short Name\" name for your $type.
The \"Short Name\" is used in many places on the Forge site, and has several restrictions.
Your $type short name:

<ul>
<li>Cannot match the short name of any other $type $project.</li>
<li>Must be between 3 and 15 characters in length</li>
<li>Must be in lower case</li>
<li>Can only contain characters, numbers, and dashes</li>
<li>Must start with a letter</li>
<li>Cannot match any reserved Novell Forge name</li>
<li>Will never change for this $type</li>
</ul>");
?>