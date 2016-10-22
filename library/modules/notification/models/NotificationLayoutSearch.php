<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\models;

use usni\library\components\TranslatedActiveDataProvider;
use yii\base\Model;
use usni\library\utils\AdminUtil;
use usni\UsniAdaptor;
/**
 * NotificationLayoutSearch class file
 * 
 * This is the search class for model NotificationLayout.
 * @package usni\library\modules\notification\models
 */
class NotificationLayoutSearch extends NotificationLayout
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return NotificationLayout::tableName();
    }

	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
                    [['name'], 'safe']
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
        $query          = NotificationLayout::find();
        $tableName      = NotificationLayout::tableName();
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
        $query->andFilterWhere(['like', 'name',   $this->name]);
        $user   = UsniAdaptor::app()->user->getUserModel();
        if(!AdminUtil::doesUserHaveOthersPermissionsOnModel(NotificationLayout::className(), $user))
        {
            $query->andFilterWhere([$tableName . '.created_by' => $user->id]);
        }
        return $dataProvider;
    }
}