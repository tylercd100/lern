<?php

namespace Tylercd100\LERN\Model;

use Illuminate\Database\Eloquent\Model;

class ExceptionModel extends Model {
    protected $table = 'vendor_tylercd100_lern_exceptions';
    protected $guarded = array('id');
}