<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\utils;

use usni\UsniAdaptor;
/**
 * Contains utility methods related to translations.
 * 
 * @package usni\library\utils
 */
class TranslationUtil
{
    /**
     * Save translated models. This is mostly used during the create scenarios.
     * @param Model $model
     * @param string $chosenLanguage
     * @return void
     */
    public static function saveTranslatedModels($model, $chosenLanguage = null)
    {
        /*
         * The scenario would be as follows.
         * 
         * In the create scenario when a model is saved, on save first the model translated
         * is saved in language decided in getLanguage function in TransalationTrait. This function
         * is invvoked after initial model save where translation models apart from chosen language
         * would be saved
         */
        if($chosenLanguage == null)
        {
            $chosenLanguage = UsniAdaptor::app()->languageManager->getContentLanguage();
        }

        $languages          = $model->getLanguages();
        //Get the translated model in the source language
        $translationModel   = $model->getTranslation();
        foreach ($languages as $language)
        {
            if($language != $chosenLanguage)
            {
                $model->setLanguage($language);
                foreach($model->translationAttributes as $attribute)
                {
                    $model->$attribute = $translationModel->$attribute;
                }
                $model->saveTranslation();
            }
        }
    }
}