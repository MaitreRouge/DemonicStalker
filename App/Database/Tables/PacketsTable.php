<?php
namespace App\Database\Tables;

use Framework\Database\Table;

class PacketsTable extends Table {

    protected string $customIdColumn = "packetId";
    protected bool $throwOnNotFound = false;
    protected string $table = "packets";

}