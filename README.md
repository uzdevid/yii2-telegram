# Extension for develop telegram bot

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org).

Either run

```
composer require uzdevid/yii2-telegram "1.0"
```

or add

```
"uzdevid/yii2-telegram": "1.0"
```

to the require section of your `composer.json` file.

## Usage

Create `telegram` extension with config params

```php
$config = [
    '_bot_' => [
        'token' => '<bot token>'
    ]
];

$telegram = new Telegram($config);
```

----------------------------------

### Set and delete webhook

- `$url` - url to Your handler

```php
$url = "https://example.com/telegrambot";
```

```php
$telegram->bot->setWebHook($url);
```

```php
$telegram->bot->deleteWebHook($url);
```

------------------------------------

### Send

#### Send text [[docs](https://core.telegram.org/bots/api#sendmessage)]

> - `$text` - [`string` | `required`] - message text.
> - `$params` - [`array` | `optional`] - additional params: parse_mode, entities, disable_web_page_preview and other. Read the telegram bot docs...
> - `$chat_id` - [`integer` | `required`] - telegram user chat id.

```php
$text = "Hello world!!!";
$params = [
    'parse_mode'=> 'HTML'
];
$chat_id = 1234567;
```

```php
$telegram->bot->sender->text($text, $params)->send($chat_id);
```

> **Note:** All send requests return a response from telegram

Example

```php
$result = $telegram->bot->sender->text($text, $params)->send($chat_id);
file_put_contents('test.json', json_encode($result, JSON_UNESCAPED_UNICODE));
```

-------------------------------------------------------------

#### Send photo [[docs](https://core.telegram.org/bots/api#sendphoto)]

> - `$photo` - [`string` | `required`] - path to photo.
> - `$params` - [`array` | `optional`] - additional params: parse_mode, caption_entities, disable_notification and other. Read the telegram bot docs...
> - `$text` - [`string` | `optional`] - caption for photo.
> - `$chat_id` - [`integer` | `required`] - telegram user chat id.

```php
$photo = '/img/elephant.jpg'
$text = "This is elephant photo";
$params = [
    'parse_mode'=> 'HTML'
];
$chat_id = 1234567;
```

```php
$telegram->bot->sender->photo($photo, $params)->text($text)->send($chat_id);
```

or you can send a photo without a caption

```php
$telegram->bot->sender->photo($photo, $params)->send($chat_id);
```

---------------------------------------------------------------

#### Send video [[docs](https://core.telegram.org/bots/api#sendvideo)]

> - `$video` - [`string` | `required`] - path to video.
> - `$params` - [`array` | `optional`] - additional params: duration, width, height and other. Read the telegram bot docs...
> - `$text` - [`string` | `optional`] - caption for video.
> - `$chat_id` - [`integer` | `required`] - telegram user chat id.

```php
$video = '/img/avengers-final.mp4'
$text = "Avengers: Final";
$params = [
    'parse_mode'=> 'HTML'
];
$chat_id = 1234567;
```

```php
$telegram->bot->sender->video($photo, $params)->text($text)->send($chat_id);
```

or you can send a video without a caption

```php
$telegram->bot->sender->video($photo, $params)->send($chat_id);
```

---------------------------------------------------------------

#### Send sticker [[docs](https://core.telegram.org/bots/api#sendsticker)]

> - `$sticker` [`string` | `required`] - sticker id.
> - `$params` - [`array` | `optional`] - additional params: disable_notification, protect_content, reply_to_message_id and other. Read the telegram bot docs...
> - `$chat_id` - [`integer` | `required`] - telegram user chat id.

```php
$sticker = "CAACAgIAAxkBAAEFRRhiz-WSsSh7GsHDlj8_csvlad9-2gACHQADO3EfIqmCmmAwV9EZKQQ";
$params = [
    'disable_notification'=> true
];
$chat_id = 1234567;
```

```php
$telegram->bot->sender->sticker($sticker, $params)->send($chat_id);
```

-------------------------------------------------------------------

#### Send contact [[docs](https://core.telegram.org/bots/api#sendcontact)]

> - `$phone` [`string` | `required`] - phone number.
> - `$first_name` [`string` | `required`] - first name.
> - `$last_name` [`string` | `optional`] - last name.
> - `$params` - [`array` | `optional`] - additional params: vcard, disable_notification, protect_content and other. Read the telegram bot docs...
> - `$chat_id` - [`integer` | `required`] - telegram user chat id.

```php
$phone = '+998993261330';
$first_name = 'Diyorbek';
$last_name = 'Ibragimov';
$params = [
    'disable_notification'=> true
];
$chat_id = 1234567;
```

```php
$telegram->bot->sender->contact($url, $first_name, $last_name)->send($chat_id);
```

-------------------------------------------------------------------------------

#### Send poll [[docs](https://core.telegram.org/bots/api#sendpoll)]

> - `$question` - [`string` | `required`] - Question.
> - `$options` - [`array` | `required`] - Options.
> - `$correct_option_id` - [`integer` | `optional`] - Correct option id, Required for polls in quiz mode.
> - `$params` - [`array` | `optional`] - additional params: type, allows_multiple_answers, explanation and other. Read the telegram bot docs...

```php
$question = "Question";
$options = ['variant id-0', 'variant id-1', 'variant id-2'];
$correct_option_id = 1;
$params = ['type' => 'quiz'];
$chat_id = 1234567;
```

```php
$telegram->bot->sender->poll($question, $options, $correct_option_id, $params)->send($chat_id);
```

### Send message, photo, video, sticker and poll with inline keyboard and/or keyboard

Keyboard

```php
$telegram->bot->sender
    ->text($text)
    ->createKeyboard([['text' => "Button"]])
    ->send($chat_id);
```

------------------------------------------------------------------------------------------------
URL inline keyboard

```php
$telegram->bot->sender
    ->photo($photo)
    ->text($text)
    ->createInlineKeyboard([['text' => "URL button", 'url' => "https://devid.uz"]])
    ->send($chat_id);
```

-----------------------------------------------------------------------------------
callback inline keyboard

```php
$callback_data = json_encode(['command' => '/callback', 'id' => 12021]);
$telegram->bot->sender
    ->text($text)
    ->createInlineKeyboard([['text' => 'callback', 'callback_date' => $callback_data]])
    ->send($chat_id);
```

----------------------------------------------------------------------------------------

### Handlers

> **Note:** When processing requests, there is no need to specify a chat id when sending a response to a request.


Processing `/start` request

```php
$telegram->bot->handler->onMessage('/start', function ($body) use ($telegram) {
    // Your code
    $telegram->bot->sender->text("Welcome")->send();
});
```

----------------------------------------------------------------------------------------
Processing callback query

```php
$telegram->bot->handler->onCommand('/callback', function ($body, $callback_data) use ($telegram) {
    // Your code
    $telegram->bot->sender->photo('/img/elephant.jpg')->send();
});
```

----------------------------------------------------------------------------------------
Processing all (*) inline query

```php
$telegram->bot->handler->onQuery('*', function ($query, $body) use ($telegram) {
    // Your code
    $title = "Mode: InlineQuery";
    $description = "Query: {$query}";
    $content = "Answer content";
    
    $telegram->bot->sender->inline
        ->answer([$telegram->bot->sender->inline->article($title, $description, $content)])
        ->send();
});
```

