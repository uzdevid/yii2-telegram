<?php

namespace uzdevid\telegram;

use uzdevid\telegram\bot\Bot;
use yii\base\Component;

/**
 * @property Bot $bot
 */
class Telegram extends Component {
    public $_bot_;

    public function getBot() {
        return new Bot(['token' => $this->_bot_['token']]);
    }
}