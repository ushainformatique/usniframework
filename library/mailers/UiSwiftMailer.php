<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\mailers;

use usni\library\utils\ArrayUtil;
use Yii;
use usni\library\modules\notification\utils\NotificationUtil;
use usni\UsniAdaptor;
/**
 * UiSwiftMailer extends default Swift mailer functionality to meet application requirements.
 *
 * @package usni\library\mailers
 */
class UiSwiftMailer extends \yii\swiftmailer\Mailer
{   
    /**
     * @inheritdoc
     */
    public $htmlLayout = '@common/mail/layouts/html';
    
    /**
     * Email Notification class.
     * @var UiEmailNotification 
     */
    public $emailNotification;
     
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $config     = NotificationUtil::getEmailSettingsFromConfig();
        $sendMethod = trim(ArrayUtil::getValue($config, 'sendingMethod', null)); 
        if($sendMethod == 'smtp')
        {
            $configurationArray = [
                                    'class'         => 'Swift_SmtpTransport',
                                    'host'          => trim(ArrayUtil::getValue($config, 'smtpHost', '')),
                                    'username'      => trim(ArrayUtil::getValue($config, 'smtpUsername', '')),
                                    'password'      => ArrayUtil::getValue($config, 'smtpPassword', ''),
                                    'port'          => trim(ArrayUtil::getValue($config, 'smtpPort', '')),
                                    'encryption'    => 'tls',
                                  ];
            $this->setTransport($configurationArray);
        }
        
        $this->useFileTransport = ArrayUtil::getValue($config, 'testMode', false);
    }

    /**
     * @inheritdoc
     */
    public function compose($view = null, array $params = [])
    {
        //Get swift message
        $message        = $this->createMessage();
        $content        = ArrayUtil::getValue($params, 'content', null);
        if($content == null)
        {
            $content        = $this->emailNotification->getBody();
        }
        $html           = $this->getView()->render($this->htmlLayout, ['content' => $content, 'message' => $message], $this);
        Yii::info('Message html is ' . $html);
        if (isset($html))
        {
            $message->setHtmlBody($html);
            if (preg_match('~<body[^>]*>(.*?)</body>~is', $html, $match)) 
            {
                $html = $match[1];
            }
            // remove style and script
            $html = preg_replace('~<((style|script))[^>]*>(.*?)</\1>~is', '', $html);
            // strip all HTML tags and decoded HTML entities
            $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, UsniAdaptor::app() ? UsniAdaptor::app()->charset : 'UTF-8');
            // improve whitespace
            $text = preg_replace("~^[ \t]+~m", '', trim($text));
            $text = preg_replace('~\R\R+~mu', "\n\n", $text);
            Yii::info('Message text is ' . $text);
            $message->setTextBody($text);
        }
        return $message;
    }
}