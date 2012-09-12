<?php
/*

FogBugz case view template

*/
?>

————————————————————————————————————————————————————————————————————————————————
<?php
$title = str_pad("[$ixBug] " . substr($sTitle, 0, 60), 80, " ", STR_PAD_BOTH);
echo tcecho($title, "grey", "on_white", "bold"), "\n";
?>
————————————————————————————————————————————————————————————————————————————————
<?php echo str_pad("$sPriority priority " . strtolower($sCategory) . " in " . $sFixFor, 80, " ", STR_PAD_BOTH), "\n"; ?>

<?php echo str_pad("Status   : " . $sStatus, 40, " ", STR_PAD_RIGHT); ?>
<?php echo str_pad("Area: " . $sProject, 40, " ", STR_PAD_LEFT), "\n"; ?>
<?php echo str_pad("Assigned : " . $sPersonAssignedTo, 80, " ", STR_PAD_RIGHT), "\n"; ?>

Full title:
<?php echo wordwrap("  " . $sTitle, 80, "\n"), "\n"; ?>

Latest summary:
<?php echo wordwrap("  " . strip_tags($sLatestTextSummary), 80, "\n"), "\n"; ?>

Last updated:
<?php echo date("  F j, Y, g:i a", strtotime($dtLastUpdated)), "\n"; ?>

<?php echo str_pad("[$host/default.asp?$ixBug]", 80, " ", STR_PAD_BOTH), "\n"; ?>
