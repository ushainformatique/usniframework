<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
use usni\library\components\UiHtml;
use backend\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta name="robots" content="noindex,nofollow"/>
        <?php echo UiHtml::csrfMetaTags() ?>
        <title><?php echo UiHtml::encode($this->title); ?></title>
        <?php $this->head() ?>
    </head>
    <body class="full-width page-condensed">
        <?php $this->beginBody() ?>
        <?php
        echo $content;
        ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>