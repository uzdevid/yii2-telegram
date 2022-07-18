<?php

namespace uzdevid\telegram\bot;

/**
 * @property Object|null $body
 * @property array $updates
 */
class Payload extends Bot {
    public function getBody() {
        return json_decode(file_get_contents("php://input"));
    }

    public function getUpdates() {
        $url = $this->buildUrlWith('getUpdates');
        return self::execute($url);
    }
}