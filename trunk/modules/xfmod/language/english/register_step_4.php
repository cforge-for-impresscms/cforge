<?php
global $type;
global $utype;

if ($type != "community")
{
  $desc = "It does not need to be as comprehensive and formal as the $utype purpose description (step 2), so f";
}
else
{
  $desc = "F";
}


define("_XF_STEP4","<p>
This is the public description of your $type.
It will be shown on the $utype Summary page, in search results, etc.
".$desc."eel free to use concise and catchy wording.
</p>");
?>