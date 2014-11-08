<?php
namespace JsMappingUtils;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class MemMon {
     
    public static function _echo_memory_usage() {
        $mem_usage = memory_get_usage(true);
        if ($mem_usage < 1024)
            echo $mem_usage." bytes";
        elseif ($mem_usage < 1048576)
            echo round($mem_usage/1024,2)." kilobytes";
        else
            echo round($mem_usage/1048576,2)." megabytes";
        echo "\n";
    }
 }
?>
