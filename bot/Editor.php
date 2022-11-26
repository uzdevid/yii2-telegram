<?php

namespace uzdevid\telegram\bot;

/**
 * @property Inline $inline
 */
class Editor extends Bot {
    private $_text;
    private $_inlineAnswer;

    public function text($text, $message_id, $params = []) {
        $params['text'] = $text;
        $params['message_id'] = $message_id;
        $this->_text = $params;
        return $this;
    }

    public function getInline() {
        return $this->_inlineAnswer == null ? $this->_inlineAnswer = new Inline(['token' => $this->token]) : $this->_inlineAnswer;
    }

    public function createKeyboard($params) {
        if (!isset($params['keyboard'])) $params['keyboard'] = $params;
        if (!isset($params['resize_keyboard'])) $params['resize_keyboard'] = true;
        if (!isset($params['one_time_keyboard'])) $params['one_time_keyboard'] = true;
        $this->reply_markup = $params;
        return $this;
    }

    public function createInlineKeyboard($params) {
        if (!isset($params['inline_keyboard'])) $params['inline_keyboard'] = $params;
        $this->reply_markup = $params;
        return $this;
    }

    public function edit($chat_id = null) {
        $url = null;
        $headers = [];
        $postFields = [];

        if ($chat_id === null) $chat_id = $this->chat_id;

        if ($this->_text !== null) list($url, $headers, $postFields) = $this->_text($chat_id);
        return self::execute($url, $headers, $postFields);
    }

    private function _text($chat_id) {
        if (!isset($this->_text['chat_id'])) $this->_text['chat_id'] = $chat_id;
        if (!isset($this->_text['reply_markup'])) $this->_text['reply_markup'] = $this->reply_markup;
        $this->_text['reply_markup'] = json_encode($this->_text['reply_markup'], JSON_UNESCAPED_UNICODE);
        $url = $this->buildUrlWith('editMessageText');
        
        $url = $url . '?' . http_build_query($this->_text);
        return [$url, [], []];
    }
}