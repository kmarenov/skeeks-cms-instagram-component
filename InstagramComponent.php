<?php
namespace skeeks\cms\instagram;

use skeeks\cms\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class InstagramComponent
 * @package skeeks\cms\instagram
 */
class InstagramComponent extends Component
{

    /**
     * @var string Access Token
     */
    public $accessToken;

    /**
     * @var int User ID
     */
    public $userId;

    /**
     * Можно задать название и описание компонента
     * @return array
     */
    static public function descriptorConfig()
    {
        return array_merge(parent::descriptorConfig(), [
            'name' => 'Настройки Instagram',
        ]);
    }

    /**
     * Файл с формой настроек, по умолчанию
     * @return string
     */
    public function getConfigFormFile()
    {
        $class = new \ReflectionClass($this->className());
        return dirname($class->getFileName()) . DIRECTORY_SEPARATOR . 'forms/_settings.php';
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['accessToken'], 'string'],
            [['userId'], 'integer'],
            [['accessToken', 'userId'], 'required'],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'accessToken' => 'Access Token',
            'userId' => 'ID пользователя',
        ]);
    }

    /**
     * Получение данных из Instagram
     * @return array
     */
    public function fetchData($userId = 0, $accessToken = '')
    {

        if (empty($accessToken)) {
            $accessToken = $this->accessToken;
        }

        if ($userId == 0) {
            $userId = $this->userId;
        }

        $result = array();

        if (empty($accessToken) || empty($userId)) {
            return $result;
        }

        $url = "https://api.instagram.com/v1/users/" . $userId . "/media/recent?access_token=" . $accessToken;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $json = curl_exec($ch);
        curl_close($ch);

        if (empty($json)) {
            return $result;
        }

        $result = json_decode($json, true);

        return $result;
    }
}