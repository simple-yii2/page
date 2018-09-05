<?php

namespace cms\page\backend\forms;

use Yii;
use yii\base\Model;
use yii\helpers\HtmlPurifier;
use cms\page\common\models\Page;

/**
 * Page editting form
 */
class PageForm extends Model
{

    /**
     * @var boolean Active.
     */
    public $active;

    /**
     * @var string Page title.
     */
    public $title;

    /**
     * @var string Alias
     */
    public $alias;

    /**
     * @var string Page content.
     */
    public $content;

    /**
     * @var Page Page model
     */
    private $_object;

    /**
     * @inheritdoc
     * @param Page $object 
     */
    public function __construct(Page $object = null, $config = [])
    {
        if ($object === null) {
            $object = new Page;
        }

        $this->_object = $object;

        //file caching
        Yii::$app->storage->cacheObject($object);

        parent::__construct(array_replace([
            'active' => $object->active == 0 ? '0' : '1',
            'title' => $object->title,
            'alias' => $object->alias,
            'content' => $object->content,
        ], $config));
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'active' => Yii::t('page', 'Active'),
            'title' => Yii::t('page', 'Title'),
            'alias' => Yii::t('page', 'Alias'),
            'content' => Yii::t('page', 'Content'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            ['active', 'boolean'],
            ['title', 'string', 'max' => 100],
            ['alias', 'match', 'pattern' => '/^[a-z0-9\-_]*$/'],
            ['content', 'string'],
            ['title', 'required'],
        ];
    }

    /**
     * Save object using model attributes
     * @return boolean
     */
    public function save()
    {
        if (!$this->validate())
            return false;

        $object = $this->_object;

        $object->active = $this->active == 1;
        $object->title = $this->title;
        $object->alias = $this->alias;
        $object->modifyDate = gmdate('Y-m-d H:i:s');

        $object->content = HtmlPurifier::process($this->content, function($config) {
            $config->set('Attr.EnableID', true);
            $config->set('HTML.SafeIframe', true);
            $config->set('URI.SafeIframeRegexp', '%^(?:https?:)?//(?:www.youtube.com/embed/|player.vimeo.com/video/|yandex.ru/map-widget/)%');
        });

        Yii::$app->storage->storeObject($object);

        if (!$object->save(false))
            return false;

        if (empty($object->alias)) {
            $object->makeAlias();
            $object->update(false, ['alias']);
        }

        return true;
    }

}
