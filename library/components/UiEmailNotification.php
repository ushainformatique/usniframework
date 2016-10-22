<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use usni\library\modules\notification\models\NotificationTemplate;
use usni\library\modules\notification\models\NotificationLayout;
use usni\UsniAdaptor;
use Yii;
use yii\log\Logger;
use usni\library\modules\notification\models\NotificationTemplateTranslated;
use usni\library\modules\notification\models\NotificationLayoutTranslated;
use yii\caching\DbDependency;
/**
 * UiEmailNotification class file.
 * 
 * @author Mayank Singhai <mayank.singhai@ushainformatique.com>
 * @package usni\library\components
 */
abstract class UiEmailNotification extends \yii\base\Component
{
    /**
     * Email body.
     * @var string
     */
    protected $body;
    /**
     * Email subject.
     * @var string
     */
    protected $subject;
    /**
     * Notification template associated with the notification.
     * @var NotificationTemplate
     */
    protected $template = null;
    /**
     * Layout for the notification.
     * @var string
     */
    protected $layout = null;
    
    /**
     * Class constructor.
     * @param Model $user
     * @param array $params
     * @return void
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->setTemplate();
        $this->setSubjectAndBody();
    }

    /**
     * Get key for the notification template in the database.
     * @throws MethodNotImplementedException
     * @return string
     */
    public function getKey()
    {
        throw new MethodNotImplementedException(__FUNCTION__, get_class($this));
    }

    /**
     * Check and set if notification template exists for the key.
     * @return void.
     */
    public function setTemplate()
    {
        $language       = UsniAdaptor::app()->languageManager->getContentLanguage();
        $tableName      = NotificationTemplate::tableName();
        $trTableName    = NotificationTemplateTranslated::tableName();
        $dependency     = new DbDependency(['sql' => "SELECT MAX(modified_datetime) FROM $tableName"]);
        $sql            = "SELECT ntt.*, nt.layout_id FROM $tableName nt, $trTableName ntt WHERE nt.notifykey = :nk AND nt.id = ntt.owner_id AND language = :lan";
        $record         = UsniAdaptor::app()->db->createCommand($sql, [':nk' => $this->getKey(), ':lan' => $language])->cache(0, $dependency)->queryOne();
        if ($record != false)
        {
            $this->template = $record;
            $this->setLayout();
        }
        else
        {
            Yii::error(UsniAdaptor::t('notification', 'The notification template is missing for key: {key}', ['{key}' => $this->getKey()]), Logger::LEVEL_ERROR);
        }
    }

    /**
     * Sets subject and body.
     * @return void
     */
    public function setSubjectAndBody()
    {
        if($this->template != false)
        {
            $this->setBody($this->prepareBodyContent());
            $this->setSubject($this->template['subject']);
        }
        else
        {
            $this->setBody($this->getDefaultContent());
            $this->setSubject($this->getDefaultSubject());
        }
    }

    /**
     * Get template data.
     * @throws MethodNotImplementedException
     */
    protected function getTemplateData()
    {
        throw new MethodNotImplementedException(__FUNCTION__, get_class($this));
    }

    /**
     * Sets the body for the email.
     * @param string $body
     * @return void
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
    
    /**
     * Gets the body for the email.
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Sets the subject for the email.
     * @param string $subject
     * @return void
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Gets module name from which notification request is made.
     * @return string
     * @throws MethodNotImplementedException
     */
    public function getModuleName()
    {
        throw new MethodNotImplementedException(__FUNCTION__, get_class($this));
    }

    /**
     * Gets delivery priority.
     * @return int
     * @throws MethodNotImplementedException
     */
    public function getDeliveryPriority()
    {
        throw new MethodNotImplementedException(__FUNCTION__, get_class($this));
    }

    /**
     * Gets the layout for the mail.
     * @return string
     */
    protected function setLayout()
    {
        if($this->template != false && $this->template['layout_id'] != 0)
        {
            $language       = UsniAdaptor::app()->languageManager->getContentLanguage();
            $tableName      = NotificationLayout::tableName();
            $trTableName    = NotificationLayoutTranslated::tableName();
            $dependency     = new DbDependency(['sql' => "SELECT MAX(modified_datetime) FROM $tableName"]);
            $sql            = "SELECT nlt.* FROM $tableName nl, $trTableName nlt WHERE nl.id = :id AND nl.id = nlt.owner_id AND nlt.language = :lan";
            $record         = UsniAdaptor::app()->db->createCommand($sql, [':id' => $this->template['layout_id'], ':lan' => $language])->cache(0, $dependency)->queryOne();
            if($record != false)
            {
                $this->layout   = $record['content'];
            }
        }
    }

    /**
     * Returns default notification content.
     * @return string
     */
    protected function getDefaultContent()
    {
        $content        = null;
        $templateData   = $this->getTemplateData();
        foreach($templateData as $key => $value)
        {
            $content .=  '<tr><td>' . str_replace(array('{' , '}'), array('', ''), $key) . '</td><td>' .
                         $value . '</td></tr>';
        }
        return UiHtml::tag('table', $content, []);
    }

    /**
     * Prepares body.
     * @return string
     */
    protected function prepareBodyContent()
    {
        $templateContent = strtr($this->template['content'], $this->getTemplateData());
        if($this->layout != null)
        {
            return strtr($this->layout, $this->getLayoutData(array('templateContent' => $templateContent)));
        }
        return $templateContent;
    }

    /**
     * Gets layout data.
     * @param array $data
     * @return array
     */
    abstract protected function getLayoutData($data);

    /**
     * Set Subject message.
     * @return string
     */
    public function getDefaultSubject()
    {
        return UsniAdaptor::app()->name . ' | ' . UsniAdaptor::t('notification', 'Default Subject');
    }
    
    /**
     * Get subject.
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }
}