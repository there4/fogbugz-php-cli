<?php


print str_repeat("—", 80) . "\n";
print tcecho(
    "  ID   Status   Est  Assigned     Title                                         ",
    'grey',
    'on_white'
) ."\n";
print str_repeat("—", 80) . "\n";

foreach ($xml->cases->case as $case) {
    $stat_color
        = (strpos($case->sStatus, 'Closed') !== FALSE)
        ? "yellow"
        : "green";

    print " ";
    print formatted(6,  "blue",      $case->ixBug);
    print formatted(9,  $stat_color, $case->sStatus);
    print formatted(4,  "white",     "[" . $case->hrsCurrEst . "]");
    print formatted(12, "white",     $case->sPersonAssignedTo);
    print "  ";
    print formatted(46, "white",     $case->sTitle) . "\n";
}
print str_repeat("—", 80) . "\n";

function formatted($width, $color, $string) {
  $string = substr($string, 0, $width);
  $string = str_pad($string, $width, " ", STR_PAD_RIGHT);
  $string = tcecho($string, $color);
}

/* End of file caseList.php */
