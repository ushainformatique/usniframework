<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */

use usni\UsniAdaptor;

$shortModelClassName = UsniAdaptor::getObjectClassName($modelClassName);
//PHP_EOL producing spaces between lines, thats why i hav'nt use it.
echo '<?php 
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace ' . $namespace . ';
    
use usni\library\components\UiSecuredActiveRecord;

/**
 * ' . $translatedModelClassName . ' class file
 *
 * @author Mayank Singhai <mayank.singhai@ushainformatique.com>
 * @author Vikash Mishra <vikash.mishra@ushainformatique.com>
 * @package ' . $namespace . '
 */
class ' . $translatedModelClassName . ' extends UiSecuredActiveRecord
{
    /**
     * @inheritdoc
     */
    public function get' . $shortModelClassName . '()
    {
        return $this->hasOne(' . $shortModelClassName . '::className(), [\'id\' => \'owner_id\']);
    }
}
?>';
