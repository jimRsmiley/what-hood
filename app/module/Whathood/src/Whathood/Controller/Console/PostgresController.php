<?php

namespace Whathood\Controller\Console;

use Whathood\Controller\BaseController;
use Whathood\Entity\NeighborhoodBoundary;
use Whathood\Timer;

class PostgresController extends BaseController
{
    public function showDatabaseSizeAction() {
        $db_human_size  = $this->m()->postgresMapper()->dbSize();
        $db_table_sizes = $this->m()->postgresMapper()->top20TableSizes();

        $tb_str = "Table Sizes:\n\n";
        foreach ($db_table_sizes as $tb_data) {
            $tb_str .= sprintf("\t%s => %s\n",$tb_data['relation'],$tb_data['size']);
        }
        return sprintf("\nTotal Whathood database size: %s\n\n$tb_str\n\n",
            $db_human_size,$tb_str);
    }
}
