<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */

use usni\library\components\UiBaseActiveRecord;

return [
            'install' => [
                            'class' => 'usni\library\modules\install\Module', 
                            'isCoreModule' => true,
                            'status'        => UiBaseActiveRecord::STATUS_ACTIVE,
                            'canBeDisabled' => false
                         ]
        ];
?>

