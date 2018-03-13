<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\helpers;

class Contest extends ActiveRecord {
    
    public function rules() {
        return array(
            array('teacher_id, audience_id, name, location_id, start_date, end_date, count_soh, count_ssuz, count_vuz, geography, report_exist, count_member_perm, count_member_othercity', 'safe'),
            array('name', 'required', 'message' => 'Имя должно быть задано'),
            array('location_id', 'required', 'message' => 'Место проведения должно быть задано'),
        );
    }
    
    
}