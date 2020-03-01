<?php

/**
 * Class Strategy
 * у нас есть несколько почтовых ящиков, и необходимо отправить данные, для каждого случая свои
 * т.е. в классе стратегия мы на основании входных данных $data['from'] определяем каким образомбудет обработан
 * входящий запросю
 */
class Strategy
{
    public static function sendMail($data)
    {
        $configs = [];
        if ($data['from'] && in_array($data['from'], ['gmail', 'mailru'])) {
            //in_array присутствует ли в массиве значение
            $configs = self::getConfig($data['from']);
            self::send($configs, $data['text']);

        }

    }

    private static function send($configs, $text)
    {

    }

    private static function getConfig($type)
    {

    }
}

$data = Strategy::sendMail(['from' => 'gmail', 'text' => 'hello world']);
