<?php

namespace cms\page\backend;

use Yii;

use cms\components\BackendModule;

/**
 * Page backend module
 */
class Module extends BackendModule
{

    /**
     * @var integer|null max page count. If set to null, there are no limit to count of pages. 
     */
    public $maxCount;

    /**
     * @inheritdoc
     */
    public static function moduleName()
    {
        return 'page';
    }

    /**
     * @inheritdoc
     */
    public static function cmsSecurity()
    {
        $auth = Yii::$app->getAuthManager();
        if ($auth->getRole('Page') === null) {
            //role
            $role = $auth->createRole('Page');
            $auth->add($role);
        }
    }

    /**
     * @inheritdoc
     */
    public function cmsMenu()
    {
        if (!Yii::$app->user->can('Page')) {
            return [];
        }

        return [
            'label' => Yii::t('page', 'Pages'),
            'url' => ['/page/page/index'],
        ];
    }

}
