<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\models;


use yii\base\Model;
use usni\library\components\TranslatedActiveDataProvider;
use usni\library\utils\AdminUtil;
use usni\UsniAdaptor;
/**
 * NotificationTemplateSearch class file.
 * This is the search class for model NotificationTemplate.
 * 
 * @package usni\library\modules\notification\models
 */
class NotificationTemplateSearch extends NotificationTemplate
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return NotificationTemplate::tableName();
    }

	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
                    [['type', 'notifykey', 'subject', 'content', 'layout_id'], 'safe']
               ];
	}

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Search based on get params.
     *
     * @return yii\data\ActiveDataProvider
     */
    public function search()
    {
        $query          = NotificationTemplate::find();
        $tableName      = NotificationTemplate::tableName();
        $query->innerJoinWith('translations');
        $dataProvider   = new TranslatedActiveDataProvider([
            'query' => $query,
        ]);

        // Validate data
        if (!$this->validate())
        {
            return $dataProvider;
        }
        $query->andFilterWhere(['language' => UsniAdaptor::app()->languageManager->getContentLanguage()]);
        $query->andFilterWhere(['like', 'type',         $this->type]);
        $query->andFilterWhere(['like', 'notifykey',    $this->notifykey]);
        $query->andFilterWhere(['like', 'subject',      $this->subject]);
        $query->andFilterWhere(['like', 'content',      $this->content]);
        $query->andFilterWhere(['like', 'layout_id',    $this->layout_id]);
        $user     = UsniAdaptor::app()->user->getUserModel();
        if(!AdminUtil::doesUserHaveOthersPermissionsOnModel(NotificationTemplate::className(), $user))
        {
            $query->andFilterWhere([$tableName . '.created_by' => $user->id]);
        }
        return $dataProvider;
    }
}