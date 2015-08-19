<?php
namespace skeeks\cms\instagram;

use MetzWeb\Instagram\Instagram;
use skeeks\cms\base\Component;
use yii\helpers\ArrayHelper;

/**
 * Class InstagramComponent
 * @package skeeks\cms\instagram
 */
class InstagramComponent extends Component
{

    /**
     * @var string Client ID
     */
    public $clientId;

    /**
     * @var int Username
     */
    public $userName;

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
            [['clientId', 'userName'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'clientId' => 'CLIENT ID для доступа к API',
            'userName' => 'Имя пользователя, фотографии которого показывать',
        ]);
    }

    /**
     * Получение данных из Instagram
     * @return array
     */
    public function fetchData($userName = '', $clientId = '')
    {
        if (empty($userName)) {
            $userName = $this->userName;
        }

        if (empty($clientId)) {
            $clientId = $this->clientId;
        }

        $result = array();

        if (empty($clientId) || empty($userName)) {
            return $result;
        }

        $instagram = new Instagram($clientId);

        $users = json_decode(json_encode($instagram->searchUser($userName, 1)), true);

        $userId = $users['data'][0]['id'];

        if (!$userId) {
            return $result;
        }

        $result = json_decode(json_encode($instagram->getUserMedia($userId, 20)), true);

        return $result;
    }
}