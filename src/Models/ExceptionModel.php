<?php

namespace Tylercd100\LERN\Models;

use Illuminate\Database\Eloquent\Model;

class ExceptionModel extends Model {
    protected $table;
    protected $guarded = array('id');

    public function __construct(array $attributes = [])
    {
        $this->table = config('lern.record.table');
        parent::__construct($attributes);
    }

    public function setDataAttribute($value){
        $this->attributes['data'] = json_encode($value);
    }

    public function getDataAttribute($value){
        return json_decode($value);
    }
}