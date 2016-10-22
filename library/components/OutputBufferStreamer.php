<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\components;

use yii\base\Component;
/**
 * OutputBufferStreamer class file.
 * 
 * The implementation of this class referenced MessageStreamer from http://zurmo.com.
 * @package usni\library\components
 */
class OutputBufferStreamer extends Component
{
    /**
     * Pad space.
     * @var int
     */
    protected $padSpace = 4096;
    /**
     * Message template.
     * @var string
     */
    protected $template = '{message}';
    /**
     * Progress bar template.
     * @var string
     */
    protected $progressBarTemplate = '{message}';

    /**
     * Class constructor.
     * @return void
     */
    public function __construct($template = null, $progressBarTemplate = null, $padSpace = 4096)
    {
        if($template != null)
        {
            $this->template = $template;
        }
        if($progressBarTemplate != null)
        {
            $this->progressBarTemplate = $progressBarTemplate;
        }
        $this->padSpace = $padSpace;
    }

    /**
     * Add the message.
     * @param string $message
     */
    public function add($message)
    {
        echo strtr($this->template, array('{message}' => $message));
        echo str_pad(' ', $this->padSpace);
        flush();
    }

    /**
     * Adds progress message.
     * @param string $message
     */
    public function addProgressMessage($message)
    {
        echo strtr($this->progressBarTemplate, array('{message}' => $message));
        flush();
    }

    /**
     * Sets the template.
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
    
    /**
     * Get template
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }
}