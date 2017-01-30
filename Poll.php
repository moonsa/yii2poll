<?php

namespace moonsa\yii2poll;

use yii;
use yii\base\Widget;

class Poll extends Widget
{

    /**
     * @var array
     */
    public $answerOptions = [ ];

    /**
     * @var
     */
    public $answerOptionsData;

    /**
     * @var array
     */
    public $answers = [ ];

    /**
     * @var Boolean
     */
    public $isExist;

    /**
     * @var Boolean
     */
    public $isVote;

    /**
     * @var array
     */
    public $params = [
        'backgroundLinesColor' => '#D3D3D3',
        'linesColor'           => '#4F9BC7',
        'linesHeight'          => 15,
        'maxLineWidth'         => 300,
    ];

    /**
     * @var array
     */
    public $pollData;

    /**
     * @var string
     */
    public $pollName = '';

    /**
     * @var int
     */
    public $sumOfVoices = 0;

    /**
     * @var array
     */
    public $ajaxSuccess = [ ];

    /**
     * @var array
     */
    public $pollState;

    /**
     * @var int
     */
    public $userId;


    /**
     * @param $name
     */
    public function setPollName($name)
    {
        $this->pollName = $name;
    }


    /**
     *
     */
    public function getDbData()
    {
        $db = Yii::$app->db;

        $command = $db->createCommand('SELECT * FROM poll_question WHERE user_id=:userId AND is_default=1')->bindParam(':userId', $this->userId);

        $this->pollData = $command->queryOne();

        $this->answerOptionsData = unserialize($this->pollData['answer_options']);
    }


    /**
     * @param $params
     */
    public function setParams($params)
    {
        $this->params = array_merge($this->params, $params);
    }


    /**
     * @param $param
     *
     * @return mixed
     */
    public function getParams($param)
    {
        return $this->params[$param];
    }


    /**
     *
     */
    public function init()
    {

        parent::init();

        $pollDB = new PollDb;

        if(empty($pollDB->isTableExists())) {
            $pollDB->createTables();
        }

        $this->getDbData();

        if ($this->pollData) {
            $this->answerOptions = $this->answerOptionsData;

            if (Yii::$app->request->isAjax) {
                if (isset( $_POST['VoicesOfPoll'] )) {
                    if ($_POST['poll_id'] == $this->pollData['id'] && isset( $_POST['VoicesOfPoll']['voice'] )) {
                        $pollDB->updateAnswers($this->pollData['id'], $_POST['VoicesOfPoll']['voice'], $this->answerOptions);

                        $pollDB->updateUsers($this->pollData['id']);
                    }
                }
            }
            $this->answers = $pollDB->getVoicesData($this->pollData['id']);


            for ($i = 0; $i < count($this->answers); $i++) {

                $this->sumOfVoices = $this->sumOfVoices + $this->answers[$i]['value'];
            }

            $this->isVote                = $pollDB->isVote($this->pollData['id']);
            $this->pollState['has_poll'] = true;
        } else {
            $this->pollState['has_poll'] = false;
        }

        $pollExplanation = new PollExplanation();

        $this->pollState['can_show_vote_form']      = $this->pollState['has_poll'] ? $pollExplanation->canShowVoteForm($this->isVote, $this->pollData,
            isset( $_POST['pollStatus'] ) ? $_POST['pollStatus'] : '', isset( $_POST['idOfPoll'] ) ? $_POST['idOfPoll'] : '') : false;
        $this->pollState['can_show_result']         = $this->pollState['has_poll'] ? $pollExplanation->canShowResult($this->isVote, $this->pollData,
            isset( $_POST['pollStatus'] ) ? $_POST['pollStatus'] : '', isset( $_POST['idOfPoll'] ) ? $_POST['idOfPoll'] : '') : false;
        $this->pollState['should_show_result']      = $this->pollState['has_poll'] ? $pollExplanation->shouldShowResult($this->isVote, $this->pollData,
            isset( $_POST['pollStatus'] ) ? $_POST['pollStatus'] : '', isset( $_POST['idOfPoll'] ) ? $_POST['idOfPoll'] : '') : false;
        $this->pollState['should_show_vote_button'] = $this->pollState['has_poll'] ? $pollExplanation->shouldShowVoteButton($this->isVote, $this->pollData,
            isset( $_POST['pollStatus'] ) ? $_POST['pollStatus'] : '', isset( $_POST['idOfPoll'] ) ? $_POST['idOfPoll'] : '') : false;
    }


    /**
     * @return string
     */
    public function run()
    {
        $model = new VoicesOfPoll;

        return $this->render('index', [
            'ajaxSuccess' => $this->ajaxSuccess,
            'answers'     => $this->answerOptions,
            'answersData' => $this->answers,
            'isVote'      => $this->isVote,
            'model'       => $model,
            'params'      => $this->params,
            'pollData'    => $this->pollData,
            'sumOfVoices' => $this->sumOfVoices,
            'pollState'   => $this->pollState
        ]);
    }
}
