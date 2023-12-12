<?php
declare(strict_types=1);

namespace Classes;

use System\DB;

class Example {
    const DEFAULT_VALUE = 1;

    public static function resetDefaultValue(){
        $res = DB::execute("UPDATE example SET some_field = ?", [DEFAULT_VALUE]);
        return $res ? "Success" : "Failure";
    }

    public static function getExample() {
        return "Example";
    }
}