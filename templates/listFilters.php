<?php

print "Fogbugz filter list for current user:\n";
print str_repeat("—", 80) . "\n";
print tcecho(
    " Filter   Type      Name                                                        ",
    'grey',
    'on_white'
) ."\n";
print str_repeat("—", 80) . "\n";

foreach ($xml->filters->children() as $filter) {
    print "  ";
    print str_pad(
        tcecho($filter['sFilter'], 'green', 'bold'),
        (8 - strlen($filter['sFilter'])),
        " ",
        STR_PAD_RIGHT
    );
    print str_pad(
        tcecho($filter['type'], 'yellow', 'bold'),
        (10 - strlen($filter['type'])),
        " ",
        STR_PAD_RIGHT
    );
    print substr($filter, 0, 60)."\n";
}

/* End of file listFilters.php */
