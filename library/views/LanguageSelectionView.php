<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\views;

use usni\library\views\UiView;
use usni\library\components\LanguageManager;
use usni\library\components\UiHtml;
use usni\UsniAdaptor;
use usni\fontawesome\FA;
use yii\bootstrap\Dropdown;
use usni\library\utils\ArrayUtil;
/**
 * LanguageSelectionView class file.
 * 
 * @package usni\library\views
 */
class LanguageSelectionView extends UiView
{
    /**
     * Selected language.
     * @var string 
     */
    public $selectedLanguage;
    
    /**
     * @inheritdoc
     */
    protected function renderContent()
    {
        $translatedLanguages  = LanguageManager::getTranslatedLanguages();
        if(empty($translatedLanguages))
        {
            return null;
        }
        
        $data           = $this->getData();
        if(count($data) == 1 && ArrayUtil::getValue($data, 'en-US') != null)
        {
            return null;
        }
        $selection      = $this->getSelectedLanguage();
        $headerLink     = UiHtml::tag('span', $data[$selection]) . "\n" .
                          FA::icon('caret-down');
        $items          = [];
        foreach($data as $key => $value)
        {
            $items[] = ['label' => $value, 'url' => '#', 'linkOptions' => ['class' => 'language-selector', 'data-language-id' => $key]];
        }
        $headerLink     = UiHtml::a($headerLink, '#', $this->getHeaderLinkOptions());

        $listItems      = Dropdown::widget(['items'         => $items,
                                            'options'       => ['class' => 'dropdown-menu dropdown-menu-right'],
                                            'encodeLabels'  => false
                                           ]);
        $content        = $headerLink . $listItems;
        return $this->wrapContent($content);
    }
    
    /**
     * Gets data
     * @return array
     */
    protected function getData()
    {
        return LanguageManager::getList();
    }


    /**
     * Wrap content
     * @param string $content
     * @return string
     */
    protected function wrapContent($content)
    {
        return '<ul class="nav pull-right btn-info"><li class="dropdown">' . $content . '</li></ul>';
    }


    /**
     * @inheritdoc
     */
    protected function registerScripts()
    {
        $url    = UsniAdaptor::app()->request->getUrl();
        $getUrl = $this->getActionUrl();
        $script = "$('.language-selector').click(function(){
                                                    var value = $(this).data('language-id');
                                                    $.ajax({
                                                            'type':'GET',
                                                            'url':'{$getUrl}' + '?language=' + value,
                                                            'success':function(data)
                                                                      {
                                                                          window.location.href = '{$url}';
                                                                      }
                                                          });
                                                 });";
        $this->getView()->registerJs($script);
    }
    
    /**
     * Get action url for change language
     * @return string
     */
    public function getActionUrl()
    {
        return UsniAdaptor::createUrl('users/default/change-language');
    }
    
    /**
     * Get selected language.
     * @return string
     */
    protected function getSelectedLanguage()
    {
        return UsniAdaptor::app()->languageManager->getContentLanguage();
    }
    
    /**
     * Get header link options
     * @return array
     */
    protected function getHeaderLinkOptions()
    {
        return array('data-toggle' => 'dropdown', 'class' => 'dropdown-toggle', 'style' => "color:white");
    }
}
