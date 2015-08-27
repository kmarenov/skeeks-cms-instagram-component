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
     * @var string Username
     */
    public $userName;

    /**
     * @var string tag
     */
    public $tag;

    /**
     * @var MetzWeb\Instagram\Instagram
     */
    public $instagram;

    /**
     * @var boolean isCacheEnabled
     */
    public $isCacheEnabled = true;

    /**
     * @var int cacheTime
     */
    public $cacheTime = 3600;

    /**
     * @var int count
     */
    public $count = 12;

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
            [['isCacheEnabled'], 'boolean'],
            [['cacheTime', 'count'], 'number'],
            [['clientId', 'userName', 'tag'], 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'clientId' => 'CLIENT ID для доступа к API',
            'userName' => 'Имя пользователя Instagram',
            'tag' => 'Тэг',
            'isCacheEnabled' => 'Включить кэширование',
            'cacheTime' => 'Время кэширования (в секундах)',
            'count' => 'Сколько фотографий показывать',
        ]);
    }

    /**
     * Получение фотографий пользователя
     * @return array
     */
    public function findMediaByUser()
    {
        $key = 'kmarenov_instagram_find_media_by_user_' . $this->userName . '_' . $this->count;

        if ($this->isCacheEnabled) {
            $media = \Yii::$app->cache->get($key);
        }

        if ($media === false || !$this->isCacheEnabled) {
            $user = $this->findUser();

            if (!empty($user)) {
                $media = json_decode(json_encode($this->instagram->getUserMedia($user['id'], $this->count)), true);
            }

            if ($this->isCacheEnabled) {
                \Yii::$app->cache->set($key, $media, $this->cacheTime);
            }
        }

        if (!empty($media['meta']['error_message'])) {
            $this->error_message = $media['meta']['error_message'];
            return array();
        }

        return $media;
    }

    /**
     * Поиск пользователя
     * @return array
     */
    public function findUser()
    {
        $userName = $this->userName;

        if (empty($userName)) {
            return array();
        }

        $key = 'kmarenov_instagram_find_user_' . $userName;

        if ($this->isCacheEnabled) {
            $user = \Yii::$app->cache->get($key);
        }

        if ($user === false || !$this->isCacheEnabled) {
            $users = json_decode(json_encode($this->instagram->searchUser($userName, 1)), true);

            if (!empty($users['meta']['error_message'])) {
                $this->error_message = $users['meta']['error_message'];
            } else {
                $user_id = $users['data'][0]["id"];

                if (!empty($user_id)) {
                    $user = json_decode(json_encode($this->instagram->getUser($user_id)), true);
                }
            }

            if ($this->isCacheEnabled) {
                \Yii::$app->cache->set($key, $user, $this->cacheTime);
            }
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

    /**
     * Получение фотографий по тэгу
     * @return array
     */
    public function findMediaByTag()
    {
        $key = 'kmarenov_instagram_find_media_by_tag_' . $this->tag . '_' . $this->count;

        if ($this->isCacheEnabled) {
            $media = \Yii::$app->cache->get($key);
        }

        if ($media === false || !$this->isCacheEnabled) {
            if (!empty($this->tag)) {
                $media = json_decode(json_encode($this->instagram->getTagMedia($this->tag, $this->count)), true);
            }

            if ($this->isCacheEnabled) {
                \Yii::$app->cache->set($key, $media, $this->cacheTime);
            }
        }

        if (!empty($media['meta']['error_message'])) {
            $this->error_message = $media['meta']['error_message'];
            return array();
        }

        return $media;
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

    public function setTag($tag)
    {
        if (!empty($tag)) {
            $this->tag = $tag;
        }
    }

    public function setCount($count)
    {
        if (!empty($count)) {
            $this->count = $count;
        }
    }
}