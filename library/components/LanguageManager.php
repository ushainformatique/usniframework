<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\UsniAdaptor;
use yii\web\Cookie;
use usni\library\utils\ArrayUtil;
/**
 * LanguageManager manages functionality related to lanuguages within the app.
 * 
 * @package usni\library\components
 */
class LanguageManager extends \yii\base\Component
{
    /**
     * The cookie name for the language for the interface. It would be the application language.
     * @var string 
     */
    public $applicationLanguageCookieName;
    /**
     * The cookie name for the language in which content of the interface would be displayed. This would exclude application language.
     * In case of frontend, it would be same as applicationLanguageCookieName most of the time.
     * @var string 
     */
    public $contentLanguageCookieName;
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if($this->applicationLanguageCookieName == null || $this->contentLanguageCookieName == null)
        {
            throw new \yii\base\InvalidConfigException(UsniAdaptor::t('application', 'missingLanguageCookie'));
        }
    }
    
    /**
     * Get allowed languages.
     * @return array
     */
    public static function getAllowed()
    {
        $table          = UsniAdaptor::tablePrefix() . 'language';
        $sql            = "SELECT l.* 
                           FROM $table l ORDER BY name";
        $connection     = UsniAdaptor::app()->getDb();
        return $connection->createCommand($sql)->queryAll();
    }
    
    /**
     * Get default language for the application.
     * @return string
     */
    public static function getDefault()
    {
        return UsniAdaptor::app()->language;
    }
    
    /**
     * Get list of languages.
     * @return array
     */
    public static function getList()
    {
        $allowedLanguages = LanguageManager::getAllowed();
        return ArrayUtil::map($allowedLanguages, 'code', 'name');
    }
    
    /**
     * Get list of languages with codes.
     * @return array
     */
    public static function getCodeList()
    {
        $languages  = self::getList();
        return array_keys($languages);
    }
    
    /**
     * Get chosen language by the user in the admin or front end.
     * @return string
     */
    public function getDisplayLanguage()
    {
        $request = UsniAdaptor::app()->getRequest();
        $value   = null;
        if(method_exists($request, 'getCookies'))
        {
            $value = $request->getCookies()->getValue($this->applicationLanguageCookieName);
        }
        if($value == null)
        {
            $value = UsniAdaptor::app()->language;
        }
        return $value;
    }
    
    /**
     * Get chosen language by the user to render content. In this case display language could be different for the application.
     * For example in admin we have display language as english but on grid view, we can change the language and check the content.
     * @return string
     */
    public function getContentLanguage()
    {
        $request = UsniAdaptor::app()->getRequest();
        $value   = null;
        if(method_exists($request, 'getCookies'))
        {
            $value = $request->getCookies()->getValue($this->contentLanguageCookieName);
        }
        if($value == null)
        {
            $value = $this->getDisplayLanguage();
        }
        return $value;
    }
    
    /**
     * Get language without locale.
     * @return string
     */
    public function getLanguageWithoutLocale()
    {
        $language       = UsniAdaptor::app()->language;
        $rawLanguage    = $language;
        if(strpos($language, '-') > 0)
        {
            $parts = explode('-', $language);
            $rawLanguage = $parts[0];
        }
        return $rawLanguage;
    }
    
    /**
     * Sets language cookie.
     * @param string $language
     * @param string $cookieName
     * @return void
     */
    public function setCookie($language, $cookieName)
    {
        $cookie = new Cookie([
                                    'name' => $cookieName,
                                    'value' => $language,
                                    'expire' => time() + 86400 * 2,
                                    'httpOnly' => true
                                ]);
        UsniAdaptor::app()->getResponse()->getCookies()->add($cookie);
    }
    
    /**
     * Get translated languages
     * @return array
     */
    public static function getTranslatedLanguages()
    {
        $allowedLanguages = self::getCodeList();
        foreach ($allowedLanguages as $index => $code)
        {
            if($code == 'en-US')
            {
                unset($allowedLanguages[$index]);
            }
        }
        if(!empty($allowedLanguages))
        {
            return array_values($allowedLanguages);
        }
        return [];
    }
}