<?php
namespace FogBugz\Cli;

class FogBugzHelpers
{
    public static function responseToArray($response)
    {
        $data = array();

        foreach (get_object_vars($response) as $property => $value) {
            switch (true) {
                case substr($property, 0, 2) == 'dt':
                    $data[$property] = (string) $value;
                    break;
                case substr($property, 0, 3) == 'hrs':
                    $data[$property] = (string) $value;
                    break;
                case substr($property, 0, 2) == 'ix':
                    $data[$property] = (int) $value;
                    break;
                case substr($property, 0, 1) == 's':
                    $data[$property] = (string) $value;
                    break;
            }
        }

        return $data;
    }
}

/* End of file FogBugzHelpers.php */
