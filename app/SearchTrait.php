<?php

namespace App;

trait SearchTrait
{

    public function scopeFindByName($query, $name) {
        return $query->where('name', 'like', '%' . $name . '%');
    }
}