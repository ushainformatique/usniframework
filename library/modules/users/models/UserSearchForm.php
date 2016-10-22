<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\users\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use usni\library\modules\users\models\User;
use usni\library\modules\auth\managers\AuthManager;
use usni\UsniAdaptor;

/**
 * UserEditForm class file
 *
 * @package usni\library\modules\users\models
 */
class UserSearchForm extends Model
{
    //User fields
    public $username;
    public $timezone;
    public $status;
    //Person fields
    public $email;
    public $firstname;
    public $lastname;
    //Address fields
    public $address1;
    public $city;
    public $country;
    public $postal_code;
    public $address2;
    public $state;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                    [['username', 'timezone', 'status', 'email', 'firstname', 'lastname',
                      'address1', 'address2', 'city', 'country', 'postal_code', 'state'], 'safe'],
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
        $query = User::find();
        $personTable        = Person::tableName();
        $addressTable       = Address::tableName();
        $query->select('tu.*')->from(User::tableName() . ' tu');
        $query->innerJoin($personTable . ' person', 'person_id = person.id');
        $query->leftJoin($addressTable . ' address', 'address.relatedmodel_id = person.id AND address.relatedmodel = :rm', [':rm' => 'Person']);
        $query->where('tu.id != :id AND tu.type = :type', [':id' => User::SUPER_USER_ID, ':type' => 'system']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // load the seach form data and validate
        if (!$this->validate())
        {
            return $dataProvider;
        }
        $user               = UsniAdaptor::app()->user->getUserModel();
        $query->andFilterWhere(['like', 'tu.username', $this->username]);
        $query->andFilterWhere(['tu.status' => $this->status]);
        $query->andFilterWhere(['tu.timezone' => $this->timezone]);
        $query->andFilterWhere(['like', 'person.firstname', $this->firstname]);
        $query->andFilterWhere(['like', 'person.lastname', $this->lastname]);
        $query->andFilterWhere(['like', 'person.email', $this->email]);
        //Address
        $query->andFilterWhere(['like', 'address.address1', $this->address1]);
        $query->andFilterWhere(['like', 'address.address2', $this->address2]);
        $query->andFilterWhere(['like', 'address.city', $this->city]);
        $query->andFilterWhere(['like', 'address.state', $this->state]);
        $query->andFilterWhere(['address.country' => $this->country]);
        if(!empty($this->groups))
        {
            $inputGroups    = $this->groups;
            $criteria->join = 'INNER JOIN tbl_group_members tgm ON tgm.member_id = t.id';
            $criteria->compare('group_id', $inputGroups);
        }
        if(!AuthManager::checkAccess($user, 'user.updateother')
            && !AuthManager::checkAccess($user, 'user.viewother')
               && !AuthManager::checkAccess($user, 'user.deleteother')
                && !AuthManager::checkAccess($user, 'user.changeotherspassword'))
        {
            $query->andFilterWhere(['tu.created_by' => $user->id]);
        }
        return $dataProvider;
    }
}
