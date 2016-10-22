<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\behaviors;

use yii\base\Behavior;

/**
 * TreeModelBehavior class file.
 * 
 * @package usni\library\behaviors
 */
class TreeModelBehavior extends Behavior
{
    use \usni\library\traits\TreeModelTrait;
}