<?php

namespace uzdevid\telegram\bot;

use CURLFile;

/**
 * @property Inline $inline
 */
class Sender extends Bot {
    private $_text;
    private $_photo;
    private $_video;
    private $_sticker;
    private $_contact;
    private $_poll;
    private $_inlineAnswer;

    public function text($text, $params = []) {
        $params['text'] = $text;
        $this->_text = $params;
        return $this;
    }

    public function photo($photo, $params = []) {
        $params['photo'] = new CURLFile($photo);
        $this->_photo = $params;
        return $this;
    }

    public function video($video, $params = []) {
        $params['video'] = new CURLFile($video);
        $this->_video = $params;
        return $this;
    }

    public function sticker($sticker, $params = []) {
        $params['sticker'] = $sticker;
        $this->_sticker = $params;
        return $this;
    }

    public function contact($phone, $first_name, $last_name = '', $params = []) {
        $params['phone_number'] = $phone;
        $params['first_name'] = $first_name;
        $params['last_name'] = $last_name;
        $this->_contact = $params;
        return $this;
    }

    public function poll($question, $options, $correct_option_id, $params = []) {
        $params['question'] = $question;
        $params['options'] = json_encode($options);
        $params['correct_option_id'] = $correct_option_id;
        $this->_poll = $params;
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

    public function send($chat_id = null) {
        $url = null;
        $headers = [];
        $postFields = [];

        if ($chat_id === null) $chat_id = $this->chat_id;

        if ($this->_photo !== null) list($url, $headers, $postFields) = $this->_photo($chat_id);
        elseif ($this->_video !== null) list($url, $headers, $postFields) = $this->_video($chat_id);
        elseif ($this->_sticker !== null) list($url, $headers, $postFields) = $this->_sticker($chat_id);
        elseif ($this->_contact !== null) list($url, $headers, $postFields) = $this->_contact($chat_id);
        elseif ($this->_poll !== null) list($url, $headers, $postFields) = $this->_poll($chat_id);
        elseif ($this->_text !== null) list($url, $headers, $postFields) = $this->_text($chat_id);

        return self::execute($url, $headers, $postFields);
    }

    private function _text($chat_id) {
        if (!isset($this->_text['chat_id'])) $this->_text['chat_id'] = $chat_id;
        if (!isset($this->_text['reply_markup'])) $this->_text['reply_markup'] = $this->reply_markup;
        $this->_text['reply_markup'] = json_encode($this->_text['reply_markup'], JSON_UNESCAPED_UNICODE);
        $url = $this->buildUrlWith('sendMessage');
        $url = $url . '?' . http_build_query($this->_text);
        return [$url, [], []];
    }

    private function _photo($chat_id) {
        if (!isset($this->_photo['chat_id'])) $this->_photo['chat_id'] = $chat_id;
        if ($this->_text !== null) $this->_photo['caption'] = $this->_text['text'];
        if (!isset($this->_photo['reply_markup'])) $this->_photo['reply_markup'] = $this->reply_markup;
        $this->_photo['reply_markup'] = json_encode($this->_photo['reply_markup'], JSON_UNESCAPED_UNICODE);

        $photo = $this->_photo['photo'];
        unset($this->_photo['photo']);

        $url = $this->buildUrlWith('sendPhoto');
        $url = $url . '?' . http_build_query($this->_photo);

        $headers = ["Content-Type:multipart/form-data"];
        $postFields = ['photo' => $photo];
        return [$url, $headers, $postFields];
    }

    private function _video($chat_id) {
        if (!isset($this->_video['chat_id'])) $this->_video['chat_id'] = $chat_id;
        if ($this->_text !== null) $this->_video['caption'] = $this->_text['text'];
        if (!isset($this->_video['reply_markup'])) $this->_video['reply_markup'] = $this->reply_markup;
        $this->_video['reply_markup'] = json_encode($this->_video['reply_markup'], JSON_UNESCAPED_UNICODE);

        $video = $this->_video['video'];
        unset($this->_video['video']);

        $url = $this->buildUrlWith('sendVideo');
        $url = $url . '?' . http_build_query($this->_video);

        $headers = ["Content-Type:multipart/form-data"];
        $postFields = ['video' => $video];
        return [$url, $headers, $postFields];
    }

    private function _sticker($chat_id) {
        if (!isset($this->_sticker['chat_id'])) $this->_sticker['chat_id'] = $chat_id;
        if (!isset($this->_sticker['reply_markup'])) $this->_sticker['reply_markup'] = $this->reply_markup;
        $this->_sticker['reply_markup'] = json_encode($this->_sticker['reply_markup'], JSON_UNESCAPED_UNICODE);

        $url = $this->buildUrlWith('sendSticker');
        $url = $url . '?' . http_build_query($this->_sticker);
        return [$url, [], []];
    }

    private function _contact($chat_id) {
        if (!isset($this->_contact['chat_id'])) $this->_contact['chat_id'] = $chat_id;
        if (!isset($this->_contact['reply_markup'])) $this->_contact['reply_markup'] = $this->reply_markup;
        $this->_contact['reply_markup'] = json_encode($this->_contact['reply_markup'], JSON_UNESCAPED_UNICODE);

        $url = $this->buildUrlWith('sendContact');
        $url = $url . '?' . http_build_query($this->_contact);
        return [$url, [], []];
    }

    private function _poll($chat_id) {
        if (!isset($this->_poll['chat_id'])) $this->_poll['chat_id'] = $chat_id;
        if (!isset($this->_poll['reply_markup'])) $this->_poll['reply_markup'] = $this->reply_markup;
        $this->_poll['reply_markup'] = json_encode($this->_poll['reply_markup'], JSON_UNESCAPED_UNICODE);

        $url = $this->buildUrlWith('sendPoll');
        $url = $url . '?' . http_build_query($this->_poll);
        return [$url, [], []];
    }
}