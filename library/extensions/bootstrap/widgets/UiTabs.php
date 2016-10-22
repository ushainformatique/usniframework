<?php

/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\extensions\bootstrap\widgets;

use yii\bootstrap\Tabs;
use usni\library\components\UiHtml;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Dropdown;

/**
 * Extends Tabs to render the tabs using bootstrap.
 * @package usni\library\extensions\bootstrap\widgets
 */
class UiTabs extends Tabs
{
    /**
     * Wrapper options.
     * @var array
     */
    public $wrapperOptions;
    /**
     * Html options for the tab content container
     * @var array 
     */
    public $tabContentContainerHtmlOptions;

    /**
     * Renders the widget.
     */
    public function run()
    {
        $content = $this->renderItems();
        echo UiHtml::tag('div', $content, $this->wrapperOptions);
        $this->registerPlugin('tab');
    }

    /**
     * @inheritodc
     */
    protected function hasActiveTab()
    {
        foreach ($this->items as $item)
        {
            if (isset($item['active']) && $item['active'] === true)
            {
                return true;
            }
        }
        //Override so that we dont have to set active attribute in the tabs configuration
        foreach ($this->items as $n => $item)
        {
            $item['active'] = true;
            $this->items[$n] = $item;
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function renderItems()
    {
        $headers = [];
        $panes = [];

        if (!$this->hasActiveTab() && !empty($this->items))
        {
            $this->items[0]['active'] = true;
        }

        foreach ($this->items as $n => $item)
        {
            if (!ArrayHelper::remove($item, 'visible', true))
            {
                continue;
            }
            if (!array_key_exists('label', $item))
            {
                throw new InvalidConfigException("The 'label' option is required.");
            }
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $label = $encodeLabel ? UiHtml::encode($item['label']) : $item['label'];
            $headerOptions = array_merge($this->headerOptions, ArrayHelper::getValue($item, 'headerOptions', []));
            $linkOptions = array_merge($this->linkOptions, ArrayHelper::getValue($item, 'linkOptions', []));

            if (isset($item['items']))
            {
                $label .= ' <b class="caret"></b>';
                UiHtml::addCssClass($headerOptions, ['widget' => 'dropdown']);

                if ($this->renderDropdown($n, $item['items'], $panes))
                {
                    UiHtml::addCssClass($headerOptions, 'active');
                }

                UiHtml::addCssClass($linkOptions, ['widget' => 'dropdown-toggle']);
                $linkOptions['data-toggle'] = 'dropdown';
                $header = UiHtml::a($label, "#", $linkOptions) . "\n"
                    . Dropdown::widget(['items' => $item['items'], 'clientOptions' => false, 'view' => $this->getView()]);
            }
            else
            {
                $options = array_merge($this->itemOptions, ArrayHelper::getValue($item, 'options', []));
                $options['id'] = ArrayHelper::getValue($options, 'id', $this->options['id'] . '-tab' . $n);

                UiHtml::addCssClass($options, ['widget' => 'tab-pane']);
                if (ArrayHelper::remove($item, 'active'))
                {
                    UiHtml::addCssClass($options, 'active');
                    UiHtml::addCssClass($headerOptions, 'active');
                }

                if (isset($item['url']))
                {
                    $header = UiHtml::a($label, $item['url'], $linkOptions);
                }
                else
                {
                    $linkOptions['data-toggle'] = 'tab';
                    $header = UiHtml::a($label, '#' . $options['id'], $linkOptions);
                }

                if ($this->renderTabContent)
                {
                    $panes[] = UiHtml::tag('div', isset($item['content']) ? $item['content'] : '', $options);
                }
            }

            $headers[] = UiHtml::tag('li', $header, $headerOptions);
        }

        return UiHtml::tag('ul', implode("\n", $headers), $this->options)
            . ($this->renderTabContent ? "\n" . UiHtml::tag('div', implode("\n", $panes), $this->tabContentContainerHtmlOptions) : '');
    }
}

?>