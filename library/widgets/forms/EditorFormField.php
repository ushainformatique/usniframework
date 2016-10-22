<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\widgets\forms;

use usni\UsniAdaptor;
use usni\library\assets\CkEditorAsset;
use usni\library\components\UiHtml;

/**
 * EditorFormField renders the CKEditor.
 * @package usni\library\widgets\forms
 */
class EditorFormField extends \yii\widgets\InputWidget
{
    /**
     * Run the widget.
     * @link{renderEditorField}
     * @return void
     */
    public function run()
    {
        $this->renderField();
    }

    /**
     * Render full name field for the user in an inline fashion.
     * @return void
     * @see http://stackoverflow.com/questions/18250404/ckeditor-strips-i-tag
     * @see http://stackoverflow.com/questions/19967092/ckeditor-4-2-2-allowedcontent-true-is-not-working
     */
    public function renderField()
    {
        //TODO @Mayank need to check
    }
//        CkEditorAsset::register($this->getView());
////        $editorJsUrl = UsniAdaptor::app()->frameworkUrl . '/ckeditor/ckeditor.js';
////        if($this->getView()-> isScriptFileRegistered($editorJsUrl) === false)
////        {
////            UsniAdaptor::app()->clientScript->registerScriptFile($editorJsUrl, UiClientScript::POS_END);
////        }
//        $fieldId = $this->resolveFieldId();
//        echo UiHtml::activeTextArea($this->model, $this->attribute, $this->options);
//
//        $script = '
//                                CKEDITOR.replace( "'. $fieldId . '",{
////         filebrowserBrowseUrl: "' . UsniAdaptor::app()->frameworkUrl . '/kcfinder/browse.php?type=files",
////         filebrowserImageBrowseUrl: "' . UsniAdaptor::app()->frameworkUrl . '/kcfinder/browse.php?type=images",
////         filebrowserFlashBrowseUrl: "' . UsniAdaptor::app()->frameworkUrl . '/kcfinder/browse.php?type=flash",
////         filebrowserUploadUrl: "' . UsniAdaptor::app()->frameworkUrl . '/kcfinder/upload.php?type=files",
////         filebrowserImageUploadUrl: "' . UsniAdaptor::app()->frameworkUrl . '/kcfinder/upload.php?type=images",
////         filebrowserFlashUploadUrl: "' . UsniAdaptor::app()->frameworkUrl . '/kcfinder/upload.php?type=flash",
//         toolbar :
//         [
//            { name: "document", items : [ "Source","-" ] },
//			{ name: "basicstyles", items : [ "Bold","Italic", "Underline","Strike","Subscript","Superscript" ] },
//			{ name: "paragraph", items : [ "NumberedList","BulletedList" ] },
//			{ name: "tools", items : [ "Maximize","-","About" ] },
//            { name: "insert", items : [ "Image","Flash","Table","PageBreak" ] },
//         ]
//    } );
//        CKEDITOR.config.allowedContent = true;
//        CKEDITOR.config.protectedSource.push(/<i[^>]*><\/i>/g);
//        CKEDITOR.config.protectedSource.push(/<span[^>]*><\/span>/g);
//        CKEDITOR.config.protectedSource.push(/<div[^>]*><\/div>/g);
//        CKEDITOR.config.protectedSource.push(/<a[^>]*><\/a>/g);
//        CKEDITOR.config.protectedSource.push(/<strong[^>]*><\/strong>/g);
//        ';
//        $this->getView()->registerJs($script);
//    }
//
//    /**
//     * Resolve field id;
//     * @return string
//     */
//    protected function resolveFieldId()
//    {
//        return UiHtml::getInputId($this->model, $this->attribute);
//    }
}
?>