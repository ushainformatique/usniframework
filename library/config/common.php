<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
return array(
    'sourceLanguage'     => 'en',
    'bootstrap'          => ['log'],
    'runtimePath'        => APPLICATION_PATH . DIRECTORY_SEPARATOR . 'runtime',
    // application components
    'components'        => [
                                'authManager' => ['class' => 'usni\library\modules\auth\managers\AuthManager'],
                                'user' => [
                                    'class' => 'usni\library\components\UiWebUser',
                                    // enable cookie-based authentication
                                    'enableAutoLogin' => true,
                                    'identityClass' => 'usni\library\modules\users\models\User',
                                ],
                                'bootstrap' => [
                                    'class' => 'bootstrap.Bootstrap',
                                ],
                                'cache' => [
                                    'class'     => 'usni\library\caching\FileCache'
                                ],
                                'assetManager' => [
                                    'class'     => 'usni\library\components\UiAssetManager',
                                    'basePath'  => '@webroot/assets',
                                    'bundles' => [
                                                            'yii\web\JqueryAsset' => [
                                                                'js' => [
                                                                    YII_ENV == YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js'
                                                                ]
                                                            ],
                                                            'yii\bootstrap\BootstrapAsset' => [
                                                                'css' => [
                                                                    YII_ENV == YII_ENV_DEV ? 'css/bootstrap.css' : 'css/bootstrap.min.css',
                                                                ]
                                                            ],
                                                            'yii\bootstrap\BootstrapPluginAsset' => [
                                                                'js' => [
                                                                    YII_ENV == YII_ENV_DEV ? 'js/bootstrap.js' : 'js/bootstrap.min.js',
                                                                ]
                                                            ]
                                                ],
                                ],
                                'i18n'          => [
                                    'class'     => 'usni\library\components\UiI18N'
                                ],
                                'db' => [
                                        'emulatePrepare'    => true,
                                        'charset'           => 'utf8',
                                        'class'             => 'usni\library\components\UiDbConnection'
                                    ],
                                'log' => [
                                            'traceLevel' => YII_DEBUG ? 3 : 0,
                                            'targets' => [
                                                [
                                                    'class' => 'yii\log\FileTarget',
                                                    'logFile' => '@runtime/logs/yii.log',
                                                    'levels' => ['error', 'warning'],
                                                    'logVars' => ['_GET', '_POST'],
                                                    'categories' => ['yii\*'],
                                                    'except'  => ['yii\db\*']
                                                ],
                                                [
                                                    'class' => 'yii\log\FileTarget',
                                                    'logFile' => '@runtime/logs/db.log',
                                                    'levels' => ['error', 'warning'],
                                                    'categories' => ['yii\db\*'],
                                                ],
                                                [
                                                    'class' => 'yii\log\FileTarget',
                                                    'logFile' => '@runtime/logs/app.log',
                                                    'levels' => ['error', 'warning'],
                                                    'logVars' => ['_GET', '_POST'],
                                                    'except'  => ['yii\db\*', 'yii\*'],
                                                ]
                                            ],
                                        ],
                                'globalDataManager'  => ['class' => 'usni\library\managers\ApplicationDataManager'],
                                'themeManager'       => ['class' => 'usni\library\components\UiThemeManager'],
                                'moduleManager'      => ['class' => 'usni\library\components\UiModuleManager'],
                                'view'               => ['class' => 'yii\web\View'],
                                'imageManager'       => ['class' => 'usni\library\components\ImageManager'],
                                'fileManager'        => ['class' => 'usni\library\components\FileManager'],
                                'videoManager'       => ['class' => 'usni\library\components\VideoManager'],
                                'installManager'     => ['class' => 'usni\library\modules\install\components\InstallManager'],
                        ],
    'params'            => [],
);
?>