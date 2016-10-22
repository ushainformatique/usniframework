<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\notification\models;

use yii\data\ActiveDataProvider;
use yii\base\Model;
use usni\library\modules\notification\models\Notification;
/**
 * NotificationSearch class file
 * This is the search class for model Notification.
 * 
 * @package usni\library\modules\notification\models
 */
class NotificationSearch extends Notification
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Notification::tableName();
    }

	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
                    [['id', 'modulename', 'type', 'data', 'status', 'priority', 'senddatetime', 'startDate', 'endDate', 'startDateHidden', 
                      'endDateHidden'], 'safe'],
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
        $query          = Notification::find();
        $dataProvider   = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Validate data
        if (!$this->validate())
        {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'modulename',       $this->modulename]);
        $query->andFilterWhere(['like', 'type',             $this->type]);
        $query->andFilterWhere(['like', 'data',             $this->data]);
        $query->andFilterWhere(['like', 'status',           $this->status]);
        $query->andFilterWhere(['like', 'priority',         $this->priority]);
        $query->andFilterWhere(['like', 'senddatetime',     $this->senddatetime]);
        $query->andFilterWhere(['like', 'startDate',        $this->startDate]);
        $query->andFilterWhere(['like', 'endDate',          $this->endDate]);
        $query->andFilterWhere(['like', 'startDateHidden',  $this->startDateHidden]);
        $query->andFilterWhere(['like', 'endDateHidden',    $this->endDateHidden]);
        return $dataProvider;
    }
}