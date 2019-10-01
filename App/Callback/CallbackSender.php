<?php


namespace App\Callback;


use App\Bot;
use App\Callback\Method\Method;
use App\Config;
use App\Exceptions\FailedRequestException;
use App\Exceptions\InvalidResponseException;

class CallbackSender
{
    /**
     * @var Config
     */
    private $config;

    /**
     * CallbackSender constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param Method $method
     * @param string|null $castTo
     * @return mixed
     * @throws FailedRequestException
     * @throws InvalidResponseException
     */
    public function send(Method $method, ?string $castTo = null)
    {
        $params = $method->params();
        $params['access_token'] = $this->config->accessToken();
        $params['v'] = Bot::API_VERSION;

        $query = http_build_query($params);
        $url = Bot::API_ENDPOINT . $method->methodName() . '?' . $query;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $json = curl_exec($curl);

        $error = curl_error($curl);
        if ($error) {
            error_log($error);
            throw new FailedRequestException($method->methodName());
        }

        curl_close($curl);

        $response = json_decode($json, true);
        if (!is_array($response) || !isset($response['response'])) {
            error_log($json);
            throw new InvalidResponseException($method->methodName(), $response);
        }

        if (!empty($castTo)) {
            return new $castTo($response['response']);
        }

        return $response['response'];
    }
}