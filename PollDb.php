<?php

namespace moonsa\yii2poll;

use yii;
use yii\db\Query;

class PollDb
{

    /**
     * @param $pollName
     * @param $userId
     *
     * @return bool
     */
    public function isPollExist($pollName, $userId)
    {
        $db      = Yii::$app->db;
        $command = $db->createCommand('SELECT * FROM poll_question WHERE poll_name=:pollName AND user_id=:userId')->bindParam(':pollName', $pollName)->bindParam(':userId', $userId);

        $pollData = $command->queryOne();

        return ( empty( $pollData ) ? false : true );
    }


    /**
     * [setVoicesData description]
     *
     * @deprecated 2.0.2 Use pollAnswerOptions()
     *
     * @param      [type] $pollName      [description]
     * @param      [type] $answerOptions [description]
     */
    public function setVoicesData($pollName, $answerOptions)
    {
        $db    = Yii::$app->db;
        $count = count($answerOptions);

        for ($i = 0; $i < $count; $i++) {
            $db->createCommand()->insert('poll_response', [
                'answers'   => $answerOptions[$i],
                'poll_name' => $pollName,
                'value'     => 0,
            ])->execute();
        }
    }


    /**
     * poll_response TBO logic
     * ADDS new answers dynamically.
     * REMOVES answers that are not part of $pollObj->answerOptionsData
     *
     * @param $pollObj
     *
     * @return int [type]          [description]
     * @throws yii\db\Exception
     */
    public function pollAnswerOptions($pollObj)
    {
        $db = Yii::$app->db;

        foreach (unserialize($pollObj['answer_options']) as $key => $value) {

            $answer = (new Query())->select([ 'answers' ])->from('poll_response')->andWhere([ 'poll_id' => $pollObj['id'] ])->andWhere([ 'answers' => $value ])->one();

            if ( ! $answer) {
                $db->createCommand()->insert('poll_response', [
                    'answers' => $value,
                    'poll_id' => $pollObj['id'],
                    'value'   => 0,
                ])->execute();
            }
        }

        return (new Query())->createCommand()->delete('poll_response',
            'poll_id = "' . $pollObj['id'] . '" AND answers NOT IN (\'' . implode(unserialize($pollObj['answer_options']), "', '") . '\')')->execute();
    }


    /**
     * @param $pollId
     *
     * @return array
     */
    public function getVoicesData($pollId)
    {
        $db         = Yii::$app->db;
        $command    = $db->createCommand('SELECT * FROM poll_response WHERE poll_id=:pollId')->bindParam(':pollId', $pollId);
        $voicesData = $command->queryAll();

        return $voicesData;
    }


    /**
     * @param $pollId
     *
     * @return array
     */
    public function getPollData($pollId)
    {
        $db         = Yii::$app->db;
        $command    = $db->createCommand('SELECT * FROM poll_question WHERE id=:id')->bindParam(':id', $pollId);
        $voicesData = $command->queryOne();

        return $voicesData;
    }


    /**
     * @param $userId
     *
     * @return array
     */
    public function getUserPolls($userId)
    {
        $db         = Yii::$app->db;
        $command    = $db->createCommand('SELECT * FROM poll_question WHERE user_id=:userId')->bindParam(':userId', $userId);
        $voicesData = $command->queryAll();

        return $voicesData;
    }


    /**
     * [updateAnswers description]
     *
     * @version  2.0.7
     * @since    na
     *
     * @param  int     $id            Poll id
     * @param  integer $voice         Integer of chosen key
     * @param  array   $answerOptions Array fo possible options
     *
     * @return object
     */
    public function updateAnswers($id, $voice, $answerOptions)
    {

        return Yii::$app->db->createCommand("
            UPDATE poll_response
            SET value = value +1  
            WHERE poll_id = '$id'
                AND answers = '$answerOptions[$voice]'")->execute();

    }


    /**
     * @param $pollId
     *
     * @return int
     * @throws yii\db\Exception
     */
    public function updateUsers($pollId)
    {
        $db = Yii::$app->db;

        return $db->createCommand()->insert('poll_user', [
            'poll_id' => $pollId,
            'user_ip' => $_SERVER['REMOTE_ADDR']
        ])->execute();
    }


    /**
     * @param $pollId
     *
     * @return bool
     */
    public function isVote($pollId)
    {
        $db      = Yii::$app->db;
        $ip      = $_SERVER['REMOTE_ADDR'];
        $command = $db->createCommand("SELECT * FROM  poll_user  WHERE user_ip='$ip' AND poll_id=:pollId")->bindParam(':pollId', $pollId);
        $result  = $command->queryOne();

        if ($result === null || $result == null || $result == '') {
            return false;
        } else {
            return true;
        }
    }


    /**
     *
     */
    public function createTables()
    {
        $db = Yii::$app->db;
        $db->createCommand("
            CREATE TABLE IF NOT EXISTS `poll_user` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `poll_id` int(11) NOT NULL,
            `user_ip` varchar(255) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `poll_id` (`poll_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8")->execute();

        $db->createCommand("
            CREATE TABLE IF NOT EXISTS `poll_question` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) UNSIGNED NOT NULL,
            `poll_name` varchar(255) NOT NULL,
            `answer_options` text NOT NULL,
            `is_default` smallint(5) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            KEY `poll_name` (`poll_name`(255))
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8")->execute();

        $db->createCommand("
            CREATE TABLE IF NOT EXISTS `poll_response` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `poll_id` int(11) UNSIGNED NOT NULL,
            `answers` varchar(128) CHARACTER SET utf8mb4 NOT NULL,
            `value` int(11) NOT NULL,
            PRIMARY KEY (`id`),
            KEY `poll_id` (`poll_id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8")->execute();
    }


    /**
     *
     */
    public function isTableExists()
    {
        $db      = Yii::$app->db;
        $command = $db->createCommand("SHOW TABLES LIKE 'poll_question'");
        $res     = $command->queryAll();

        return $res;
    }


    public function setPollDefault($pollId, $userId)
    {
        Yii::$app->db->createCommand("UPDATE poll_question SET is_default = 0 WHERE user_id=" . $userId . "")->execute();

        return Yii::$app->db->createCommand("UPDATE poll_question SET is_default = 1 WHERE id=" . $pollId . "")->execute();
    }


    public function getUserDefaultPoll($userId)
    {
        $db       = Yii::$app->db;
        $command  = $db->createCommand('SELECT * FROM poll_question WHERE user_id=:userId AND is_default=1')->bindParam(':userId', $userId);
        $question = $command->queryOne();

        return $question;
    }


    /**
     * @param VoicesOfPoll $data
     *
     * @return $this
     */
    public function savePoll($data)
    {

        try {
            $db = Yii::$app->db;

            Yii::$app->db->createCommand("UPDATE poll_question SET is_default = 0 WHERE user_id=" . $data->user_id . "")->execute();

            $db->createCommand()->insert('poll_question', [
                'user_id'        => $data->user_id,
                'poll_name'      => $data->poll_name,
                'answer_options' => serialize($data->answer_options),
                'is_default'     => true
            ])->execute();

            $question = $db->createCommand('SELECT * FROM poll_question WHERE user_id=:userId AND is_default=1')->bindParam(':userId', $data->user_id)->queryOne();

            return $this->pollAnswerOptions($question);
        } catch (\Exception $ex) {
            die( json_encode($ex->getMessage()) );
        }
    }


    public function deletePoll($id)
    {
        return (new Query())->createCommand()->delete('poll_question', 'id = "' . $id . '"')->execute();
    }
}
