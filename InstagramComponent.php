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

    function init()
    {
        parent::init();
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
        $key = 'kmarenov_instagram_find_media_by_user_' . $this->userName;

        $media = \Yii::$app->cache->get($key);

        if ($media === false) {
            $user = $this->findUser();

            if (!empty($user)) {
                $media = json_decode(json_encode($this->instagram->getUserMedia($user['id'], 12)), true);
            }

            \Yii::$app->cache->set($key, $media, 3600);
        }

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

        $key = 'kmarenov_instagram_find_user_' . $userName;

        $user = \Yii::$app->cache->get($key);

        if ($user === false) {
            $users = json_decode(json_encode($this->instagram->searchUser($userName, 1)), true);

            if (!empty($users['meta']['error_message'])) {
                $this->error_message = $users['meta']['error_message'];
            } else {
                $user_id = $users['data'][0]["id"];

                if (!empty($user_id)) {
                    $user = json_decode(json_encode($this->instagram->getUser($user_id)), true);
                }
            }

            \Yii::$app->cache->set($key, $user, 3600);
        }

        if (!empty($user['meta']['error_message'])) {
            $this->error_message = $user['meta']['error_message'];
            return array();
        }

        if (empty($user['data'])) {
            return array();
        }

        return $user['data'];
    }

    public function setUserName($userName)
    {
        if (!empty($userName)) {
            $this->userName = $userName;
        }
    }

    public function setClientId($clientId)
    {
        if (!empty($clientId)) {
            $this->clientId = $clientId;
            $this->instagram->setApiKey($clientId);
        }
    }
}