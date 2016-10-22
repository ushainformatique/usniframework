<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\models;

use yii\base\Model;
use usni\library\modules\auth\models\Group;
use usni\library\utils\AdminUtil;
use usni\UsniAdaptor;
use usni\library\dataproviders\TreeTranslatedActiveDataProvider;
/**
 * GroupSearch class file
 * This is the search class for model Group.
 * 
 * @package usni\library\modules\auth\models
 */
class GroupSearch extends Group
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Group::tableName();
    }

	/**
     * @inheritdoc
     */
	public function rules()
	{
		return [
                    [['name', 'status', 'parent_id', 'level', 'language'], 'safe'],
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
        $query          = Group::find();
        $query->innerJoinWith('translations');
        $dataProvider   = new TreeTranslatedActiveDataProvider([
            'query' => $query,
            'filterModel' => $this,
            'filteredColumns' => ['name', 'status', 'parent_id', 'level', 'created_by']
        ]);

        // Validate data
        if (!$this->validate())
        {
            return $dataProvider;
        }
        $this->language = UsniAdaptor::app()->languageManager->getContentLanguage();
        $user     = UsniAdaptor::app()->user->getUserModel();
        if(!AdminUtil::doesUserHaveOthersPermissionsOnModel(Group::className(), $user))
        {
            $this->created_by = $user->id;
        }
        return $dataProvider;
    }
}