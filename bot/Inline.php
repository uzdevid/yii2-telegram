<?php

namespace uzdevid\telegram\bot;

class Inline extends Bot {
    private $_response;

    public function article($title, $description, $content_text, $content_parse_mode = 'HTML', $params = []) {
        $params['id'] = uniqid();
        $params['type'] = 'article';
        $params['title'] = $title;
        $params['description'] = $description;
        $params['input_message_content'] = ['message_text' => $content_text, 'parse_mode' => $content_parse_mode];
        return $params;
    }

    public function answer($results, $params = []) {
        if (!isset($params['inline_query_id'])) $params['inline_query_id'] = $this->payload->body->inline_query->id;
        $params['results'] = json_encode($results);
        $this->_response = $params;
        return $this;
    }

    public function send() {
        $url = $this->buildUrlWith('answerInlineQuery');
        $url = $url . '?' . http_build_query($this->_response);
        return self::execute($url);
    }
}