<?php

namespace  moonsa\yii2poll;

use yii\base\Model;

class VoicesOfPoll extends Model
{

    public $answer_options = '';

    public $poll_name = '';

    public $user_id;

    public $voice;

    public $type;


    public function attributeLabels()
    {
        return [
            'voice' => '',
            'type'  => '',
        ];
    }


    public function save()
    {
        $pollDB = new PollDb();

        if ($pollDB->isPollExist($this->poll_name, $this->user_id)) {
            throw new \Exception;
        }
		
        $pollDB->savePoll($this);
    }
}
