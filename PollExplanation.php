<?php

namespace moonsa\yii2poll;

use Yii;

class PollExplanation
{

    /**
     * @param $isVote
     * @param $pollData
     * @param $pollStatus
     * @param $pollId
     *
     * @return bool
     */
    public function canShowVoteForm($isVote, $pollData, $pollStatus, $pollId)
    {
        if (( $isVote == false && $pollStatus != 'show' ) || ( $pollId == $pollData['id'] && $pollStatus != 'show' && $pollStatus == 'vote' )) {
            return true;
        }

        return false;
    }


    /**
     * @param $isVote
     * @param $pollData
     * @param $pollStatus
     * @param $pollId
     *
     * @return bool
     */
    public function canShowResult($isVote, $pollData, $pollStatus, $pollId)
    {
        if (( $isVote == false && $pollStatus != 'show' ) || ( $pollStatus != 'show' ) || ( $pollId == $pollData['id'] && $pollStatus != 'show' ) && $pollStatus == 'vote') {
            return true;
        }

        return false;
    }


    /**
     * @param $isVote
     * @param $pollData
     * @param $pollStatus
     * @param $pollId
     *
     * @return bool
     */
    public function shouldShowResult($isVote, $pollData, $pollStatus, $pollId)
    {
        if ($isVote == true || ( $pollId == $pollData['id'] && $pollStatus == 'show' )) {
            return true;
        }

        return false;
    }


    /**
     * @param $isVote
     * @param $pollData
     * @param $pollStatus
     * @param $pollId
     *
     * @return bool
     */
    public function shouldShowVoteButton($isVote, $pollData, $pollStatus, $pollId)
    {
        if($isVote == false && $pollId == $pollData['id'] && $pollStatus == 'show') {
            return true;
        }

        return false;
    }

}
