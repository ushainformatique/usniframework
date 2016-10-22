<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\models;

use yii\data\ActiveDataProvider;
use yii\base\Model;
use usni\library\utils\AdminUtil;
use usni\UsniAdaptor;
/**
 * RoleSearch class file
 * 
 * @package usni\library\modules\auth\models
 */
class RoleSearch extends Role
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Role::tableName();
    }

	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
                    [['name', 'status', 'parent_id', 'level'], 'safe'],
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
        $query          = Role::find();
        $tableName      = Role::tableName();
        $dataProvider   = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Validate data
        if (!$this->validate())
        {
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'name',      $this->name]);
        $query->andFilterWhere(['like', 'status',    $this->status]);
        $query->andFilterWhere(['like', 'parent_id', $this->parent_id]);
        $query->andFilterWhere(['like', 'level',     $this->level]);
        $user     = UsniAdaptor::app()->user->getUserModel();
        if(!AdminUtil::doesUserHaveOthersPermissionsOnModel(Role::className(), $user))
        {
            $query->andFilterWhere([$tableName . '.created_by' => $user->id]);
        }
        return $dataProvider;
    }
}