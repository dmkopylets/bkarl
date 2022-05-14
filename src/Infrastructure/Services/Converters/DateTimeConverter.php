<?php

namespace App\Infrastructure\Services\Converters;

use App\Application\Service\Date\DateConst;

class DateTimeConverter

{

    public function convert($dateTimeValue)
    {
        return $dateTimeValue->format(DateConst::DATE_TIME_ISO);
    }

//    public function convertSQL($dateTimeValue)
//    {
//
//        return  to_char($dateTimeValue, 'YYYY-MM-DD"T"HH24:MI:SSOF');
//    }


}