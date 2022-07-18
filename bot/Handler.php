<?php

namespace uzdevid\telegram\bot;

class Handler extends Bot {
    public function onMessage($message, $callback) {
        $body = $this->payload->body;
        if ($body !== null && $body->message->text == $message) $callback($body);
    }

    public function onCommand($command, $callback) {
        $body = $this->payload->body;
        if ($body === null) return null;
        if (!isset($body->callback_query)) return null;

        $callback_data = $body->callback_query->data;
        if ($callback_data == $command)
            return $callback($body, $callback_data);

        $callback_data = json_decode($callback_data);
        if (isset($callback_data->command) && $callback_data->command == $command)
            return $callback($body, $callback_data);
    }

    public function onQuery($command, $callback) {
        $body = $this->payload->body;
        if ($body === null) return null;
        if (!isset($body->inline_query) || isset($body->inline_query) && empty($body->inline_query->query)) return null;
        if ($command != '*' && !preg_match("/{$command}/", $body->inline_query->query)) return null;

        $callback($body->inline_query->query, $body);
    }

    public function onAction($callback) {
        $body = $this->payload->body;
        $callback($body);
    }
}