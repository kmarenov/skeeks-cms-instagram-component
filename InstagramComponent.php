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
     * @var MetzWeb\Instagram\Instagram
     */
    public $instagram;

    /**
     * @var string
     */
    public $error_message;

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

    function init($userName = '', $clientId = '')
    {
        parent::init();

        if (!empty($userName)) {
            $this->userName = $userName;
        }

        if (!empty($clientId)) {
            $this->clientId = $clientId;
        }

        $this->instagram = new Instagram($this->clientId);
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
     * Получение фотографий пользователя из Instagram
     * @return array
     */
    public function findMediaByUser()
    {
        $user = $this->findUser();

        if (empty($user)) {
            return array();
        }

        $media = json_decode(json_encode($this->instagram->getUserMedia($user['id'], 12)), true);

        if (!empty($media['meta']['error_message'])) {
            $this->error_message = $media['meta']['error_message'];
            return array();
        }

        return $media;
    }

    /**
     * Поиск пользователя Instagram
     * @return array
     */
    public function findUser()
    {
        $userName = $this->userName;

        if (empty($userName)) {
            return array();
        }

        $users = json_decode(json_encode($this->instagram->searchUser($userName, 1)), true);

        if (!empty($users['meta']['error_message'])) {
            $this->error_message = $users['meta']['error_message'];
            return array();
        }

        $user = $users['data'][0];

        if (empty($user)) {
            return array();
        }

        return $user;
    }
}