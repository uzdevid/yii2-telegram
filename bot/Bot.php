<?php

namespace uzdevid\telegram\bot;

use uzdevid\telegram\Telegram;

/**
 * @property Sender $sender
 * @property Editor $editor
 * @property Payload $payload
 * @property Handler $handler
 * @property int $chat_id
 */
class Bot extends Telegram {
    public $token;
    private $_chat_id;
    public $reply_markup = ['remove_keyboard' => true];
    const endpointUrl = "https://api.telegram.org/bot{token}";

    public function getSender() {
        return new Sender(['token' => $this->token]);
    }

    public function getEditor() {
        return new Editor(['token' => $this->token]);
    }

    public function getChat_id() {
        if (isset($this->payload->body->message)) $this->_chat_id = $this->payload->body->message->chat->id;
        elseif (isset($this->payload->body->callback_query)) $this->_chat_id = $this->payload->body->callback_query->message->chat->id;
        elseif (isset($this->payload->body->inline_query)) $this->_chat_id = $this->payload->body->inline_query->form->id;

        return $this->_chat_id;
    }

    public function getPayload() {
        return new Payload(['token' => $this->token]);
    }

    public function getHandler() {
        return new Handler;
    }

    public function buildUrlWith($method) {
        $url = str_replace('{token}', $this->token, self::endpointUrl);
        return $method ? $url . "/${method}" : $url;
    }

    protected function execute($url, $headers = [], $post_fields = []) {
        $curl = curl_init($url);
        if (!empty($headers)) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if (!empty($post_fields)) curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response, true);
    }

    public function setWebHook($webhook_url) {
        $url = $this->buildUrlWith('setWebHook');
        $params = ['url' => $webhook_url];
        $url = $url . '?' . http_build_query($params);

        return $this->execute($url);
    }

    public function deleteWebHook($webhook_url) {
        $url = $this->buildUrlWith('deleteWebHook');
        $params = ['url' => $webhook_url];
        $url = $url . '?' . http_build_query($params);

        return $this->execute($url);
    }
}