<?php

namespace App\Models;

use Core\Model;

class SchoolSetting extends Model
{
    protected string $table = 'school_settings';

    public function get()
    {
        return $this->find(1);
    }

    public function updateSettings($data)
    {
        return $this->update(1, $data);
    }
}
