<?php

namespace chalvik\test;

class Sms  extends Component
{
    const API_URL = 'http://bytehand.com:3800/';
    const VERSION = 1.0;

    /**
     * Идентификатор клиента
     * @var $id
     */
    public $id;

    /**
     * Ключ клиента
     * @var $key
     */
    public $key;

    /**
     * Выполнение запроса к сервису
     * @param $method Название метода
     * @param $params Набор дополнительных параметров
     *
     * @return mixed Ответ от сервиса
     * @throws Exception
     */
    private function executeRequest($method, $params = array())
    {
        $request = self::API_URL . $method . '?' . http_build_query(
            array_merge(
                array('key' => $this->key,
                      'id'  => $this->id),
                $params
            )
        );
        $response = file_get_contents($request);
//        $response = $this->file_get_contents_curl($request);
        
        if (!$response) {
            throw new Exception('Response was empty');
        }
        $response = json_decode($response);
        if (!$response) {
            throw new \yii\web\HttpException(403, 'Response was incorrect'); 
        }

        return $response->description;
    }

    /**
     * Отправка сообщения
     * @param $to      Номер получателя
     * @param $from    Номер отправителя
     * @param $message Текст сообщения
     *
     * @return mixed $id Код сообщения
     */
    public function sendMessage($to, $from, $message)
    {   
        return $this->executeRequest(
            'send', array(
                         'to'      => $to,
                         'from'    => $from,
                         'text' => urlencode($message)
                    )
        );
    }

    /**
     * Проверка статуса сообщений
     * @param $id Код сообщения
     *
     * @return mixed $status Статус сообщения
     */
    public function checkStatus($id)
    {
        return $this->executeRequest('status', array('message' => $id));
    }

    /**
     * Получение текущего баланса
     * @return mixed $balance Баланс лицевого счёта
     */
    public function getBalance()
    {
        return $this->executeRequest('balance');
    }
    
    
    private function file_get_contents_curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Устанавливаем параметр, чтобы curl возвращал данные, вместо того, чтобы выводить их в браузер.
        curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }    
    
}

?>
