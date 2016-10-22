<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\components\UiHtml;
/**
 * Lightbox implements the lightbox2 functionality in Yii2
 *
 * @author Mayank Singhai <mayank.singhai@ushainformatique.com>
 */
class Lightbox extends \branchonline\lightbox\Lightbox
{
    /**
     * Container into which all images are stored
     * @var string 
     */
    public $containerTag = 'div';
    
    /**
     * Html options for the container
     * @var array 
     */
    public $containerOptions = [];
    
    /**
     * @inheritdoc
     */
    public function run()
    {
        $html = '';
        foreach ($this->files as $file)
        {
            if (!isset($file['thumb']) || !isset($file['original']))
            {
                continue;
            }

            $attributes = [
                'data-title' => isset($file['title']) ? $file['title'] : '',
                //Add this so that class can be added to each link
                'class'      => isset($file['class']) ? $file['class'] : '',
            ];

            if (isset($file['group']))
            {
                $attributes['data-lightbox'] = $file['group'];
            }
            else
            {
                $attributes['data-lightbox'] = 'image-' . uniqid();
            }
            $thumbAttributes = [
                //Add this so that class can be added to each image
                'class'      => isset($file['thumbclass']) ? $file['thumbclass'] : '',
                'id'         => isset($file['id']) ? $file['id'] : '',
            ];
            $img    = UiHtml::img($file['thumb'], $thumbAttributes);
            $a      = UiHtml::a($img, $file['original'], $attributes);
            if($file['itemTag'] != null)
            {
                $itemOptions = ArrayUtil::getValue($file, 'itemOptions', []);
                $a = UiHtml::tag($file['itemTag'], $a, $itemOptions);
            }
            $html .= $a;
        }
        if($this->containerTag != null)
        {
            $html = UiHtml::tag($this->containerTag, $html, $this->containerOptions);
        }
        return $html;
    }
}