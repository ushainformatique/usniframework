<?php
/**
 * @copyright Copyright (C) 2016 Usha Singhai Neo Informatique Pvt. Ltd
 * @license https://www.gnu.org/licenses/gpl-3.0.html
 */
namespace usni\library\modules\auth\managers;

use usni\UsniAdaptor;
use usni\library\modules\users\models\User;
use usni\library\modules\auth\models\Group;
use usni\library\modules\auth\models\AuthAssignment;
use usni\library\utils\DAOUtil;
use usni\library\modules\auth\models\AuthPermission;
use usni\library\utils\ArrayUtil;
use usni\library\modules\auth\models\GroupMember;
use usni\library\components\UiSecuredModule;
use usni\library\exceptions\FailedToSaveModelException;
use yii\base\NotSupportedException;
use usni\library\utils\CacheUtil;
use yii\caching\DbDependency;
/**
 * Application component that handles the functionality related to authorization in the application.
 * 
 * @package usni\library\modules\auth\components
 */
class AuthManager extends \yii\base\Component
{
    const AUTH_IDENTITY_TYPE_GROUP = 'group';
    const AUTH_IDENTITY_TYPE_USER  = 'user';
    const AUTH_IDENTITY_TYPE_ROLE  = 'role';

    public $groups = [];

    /**
     * Adds resource permission.
     * @param string $permission
     * @param string $resource
     * @param string $moduleId
     * @param string $alias
     * @return boolean
     */
    public static function addResourcePermission($permission, $resource, $moduleId, $alias)
    {
        if(self::doesResourcePermissionExist($permission, $resource) === false)
        {
            $authPermission           = new AuthPermission(['scenario' => 'create']);
            $authPermission->resource = $resource;
            $authPermission->name     = $permission;
            $authPermission->module   = $moduleId;
            $authPermission->alias    = $alias;
            if($authPermission->save())
            {
                return true;
            }
            else
            {
                throw new FailedToSaveModelException(AuthPermission::className());
            }
        }
        return true;
    }

    /**
     * Add modules permissions.
     * @param $useCache boolean
     * @return void
     */
    public static function addModulesPermissions($useCache = true)
    {
        UsniAdaptor::db()->createCommand()->truncateTable(AuthPermission::tableName())->execute();
        $modules = UsniAdaptor::app()->moduleManager->getInstantiatedModules();
        $finalPermissions       = [];
        foreach($modules as $key => $module)
        {
            if($module instanceof UiSecuredModule)
            {
                $modulePermissionsSet = static::getModulePermissions($module->id);
                foreach($modulePermissionsSet as $resource => $permissionSet)
                {
                    foreach($permissionSet as $permission => $alias)
                    {
                        $finalPermissions[] = [$permission, $resource, $module->id, $alias, User::SUPER_USER_ID, date('Y-m-d H:i:s')];
                    }
                }
            }
        }
        $table      = UsniAdaptor::tablePrefix() . 'auth_permission';
        $columns    = ['name', 'resource', 'module', 'alias', 'created_by', 'created_datetime'];
        try
        {
            UsniAdaptor::app()->db->createCommand()->batchInsert($table, $columns, $finalPermissions)->execute();
        }
        catch (\yii\db\Exception $e)
        {
            throw $e;
        }
        return true;
    }

    /**
     * Checks if a resource permission exists.
     * @param string $permission
     * @param string $resource
     * @return void
     */
    public static function doesResourcePermissionExist($permission, $resource)
    {
        $query = AuthPermission::find();
        $query->where('name = :name AND resource = :resource');
        $query->params([':name' => $permission, ':resource' => $resource]);
        if($query->count() == 0)
        {
            return false;
        }
        return true;
    }

    /**
     * Get permissions for the module.
     * @param \yii\base\Module $module
     * @return boolean
     */
    public static function addModulePermissions($module)
    {
        $moduleClassName   = get_class($module);
        $permissions       = static::getModulePermissions($module->id);
        foreach($permissions as $resource => $permissionSet)
        {
            foreach($permissionSet as $permission => $alias)
            {
                self::addResourcePermission($permission, $resource, $module->id, $alias);
            }
        }
        CacheUtil::set($moduleClassName . 'ModulePermissions', $permissions);
        return true;
    }

    /**
     * Add auth assignments.
     * @param array $permissions
     * @param int $identityName
     * @param string $identityType
     * @return void
     */
    public static function addAuthAssignments($permissions, $identityName, $identityType)
    {
        $user           = UsniAdaptor::app()->user->getUserModel();
        if($user == null)
        {
            $userId = User::SUPER_USER_ID;
        }
        else
        {
            $userId = $user->id;
        }
        self::deleteAuthAssignments(null, $identityName, $identityType);
        $tableName      = UsniAdaptor::app()->db->tablePrefix. 'auth_assignment';
        $batchData      = [];
        foreach ($permissions as $permission)
        {
            $authPermission         = static::getPermissionByName($permission);
            $data['identity_type']  = $identityType;
            $data['identity_name']  = $identityName;
            $data['permission']     = $permission;
            $data['resource']       = $authPermission['resource'];
            $data['module']         = $authPermission['module'];
            $data['created_by']     = $userId;
            $data['created_datetime']     = date('Y-m-d H:i:s');
            $batchData[]            = $data;
        }
        $columns    = ['identity_type', 'identity_name', 'permission', 'resource', 'module', 'created_by', 'created_datetime'];
        try
        {
            UsniAdaptor::app()->db->createCommand()->batchInsert($tableName, $columns, $batchData)->execute();
        }
        catch(\yii\db\Exception $e)
        {
            throw $e;
        }
    }

    /**
     * Get auth assignments.
     * @param string $identityName
     * @param string $identityType
     * @return array
     */
    public static function getAuthAssignments($identityName, $identityType)
    {
        $tableName = AuthAssignment::tableName();
        $sql       = "SELECT * FROM $tableName WHERE identity_type = :aot AND identity_name = :aon";
        $records   = UsniAdaptor::app()->db->createCommand($sql, [':aot' => $identityType, ':aon' => $identityName])->queryAll();
        $assignedPermissions = [];
        foreach($records as $record)
        {
            $assignedPermissions[] = $record['permission'];
        }
        return $assignedPermissions;
    }

    /**
     * Gets identity class name by type.
     * @param string $identityType
     * @return string
     * @throws NotSupportedException
     */
    public static function getIdentityClassNameByType($identityType)
    {
        if($identityType == 'group')
        {
            return Group::className();
        }
        elseif($identityType == 'user')
        {
            return User::className();
        }
        else
        {
            throw new NotSupportedException();
        }
    }

    /**
     * Gets auth identity object.
     * @param int $identityId
     * @param string $identityType
     * @return IAuthIdentity Model implementing IAuthIdentity interface
     */
    public static function getAuthIdentity($identityId, $identityType)
    {
        $authIdentityClassName   = self::getIdentityClassNameByType($identityType);
        return $authIdentityClassName::findOne($identityId);
    }

    /**
     * Delete auth assignements.
     * @param string $resource
     * @param string $identityName
     * @param string $identityType
     * @return void
     */
    public static function deleteAuthAssignments($resource = null, $identityName, $identityType)
    {
        if($resource != null)
        {
            $condition = 'resource = :resource AND identity_type =:itype AND identity_name = :iname';
            $params    = [':resource' => $resource, ':itype' => $identityType, ':iname' => $identityName];
        }
        else
        {
            list($condition, $params) = self::getAssignmentCriteriaByAuthIdentity($identityType, $identityName);
        }
        AuthAssignment::deleteAll($condition, $params);
    }

    /**
     * Add group members.
     * @param Group $group
     * @param array $members
     * @return void
     * @throws NotSupportedException
     */
    public static function addGroupMembers($group, $members)
    {
        self::deleteGroupMembers($group);
        foreach($members as $member)
        {
            $memberData = explode('-', $member);
            $groupMember              = new GroupMember(['scenario' => 'create']);
            $groupMember->member_type = $memberData[0];
            $groupMember->member_id   = $memberData[1];
            $groupMember->group_id    = $group->id;
            $groupMember->save();
        }
    }

    /**
     * Delete group members.
     * @param Group $group
     * @return void
     */
    public static function deleteGroupMembers($group)
    {
        GroupMember::deleteAll('group_id = :gid', [':gid' => $group->id]);
    }

    /**
     * Get group members.
     * @param Group $group
     * @return array
     */
    public static function getGroupMembers($group)
    {
        $table      = UsniAdaptor::tablePrefix() . 'group_member';
        $dependency = new DbDependency(['sql' => "SELECT MAX(modified_datetime) FROM $table"]);
        $sql        = "SELECT * FROM $table WHERE group_id = :gid";
        return UsniAdaptor::app()->db->createCommand($sql, [':gid' => $group->id])->cache(0, $dependency)->queryAll();
    }

    /**
     * Gets user effective permissions.
     * @param User $user
     */
    public static function getUserEffectiveAuthAssignments($user)
    {
        $permissions    = [];
        $memberRecords  = array_keys(self::getUserGroups($user->id, get_class($user)));
        if(!empty($memberRecords))
        {
            $authAssignments    = array();
            foreach($memberRecords as $groupId)
            {
                $authAssignments = ArrayUtil::merge($authAssignments,
                                                    self::getAuthAssignementsByGroup($groupId));
            }
            foreach($authAssignments as $authAssignment)
            {
                $permissions[] = $authAssignment['permission'];
            }
        }
        //Get permissions by user
        $userPerms = static::getAuthAssignmentsByUser($user);
        return array_merge($permissions, $userPerms);
    }

    /**
     * Gets auth assignment criteria.
     * @param string $authType
     * @param string $authName
     * @return array
     */
    public static function getAssignmentsByAuthIdentity($authType, $authName)
    {
        return AuthAssignment::find()->asArray()->where('identity_name = :iname AND identity_type = :itype',
                                   [':iname' => $authName, ':itype' => $authType])->all();
    }

    /**
     * Process and get assignement records for group.
     * @param int $groupId
     * @return array
     */
    public static function getAuthAssignementsByGroup($groupId)
    {
        $group              = Group::findOne($groupId);
        list($condition, $params)   = self::getAssignmentCriteriaByAuthIdentity(AuthManager::AUTH_IDENTITY_TYPE_GROUP,
                                                                        $group->name);
        $records             = AuthAssignment::find()->where($condition, $params)->asArray()->all();
        $childGroups         = DAOUtil::getAllChildrens($group->id, $group);
        $childRecords        = array();
        foreach($childGroups as $childGroup)
        {
            list($childCondition, $childParams) = self::getAssignmentCriteriaByAuthIdentity(AuthManager::AUTH_IDENTITY_TYPE_GROUP,
                                                                        $childGroup['name']);
            $rows           = AuthAssignment::find()->where($childCondition, $childParams)->asArray()->all();
            $childRecords   = ArrayUtil::merge($childRecords, $rows);
        }
        return ArrayUtil::merge($childRecords, $records);
    }

    /**
     * Gets user auth assignments.
     * @param User $user
     * @return array
     */
    public static function getAuthAssignmentsByUser($user)
    {
        $authAssignments    = self::getAssignmentsByAuthIdentity($user->getAuthType(),
                                                                 $user->getAuthName());
        $permissions        = array();
        foreach($authAssignments as $authAssignment)
        {
            $permissions[]  = $authAssignment['permission'];
        }
        return $permissions;
    }

    /**
     * Removes modules permissions.
     * @return void.
     */
    public static function removeModulesPermissions()
    {
        AuthPermission::deleteAll();
    }

    /**
     * Gets module permissions.
     * @param string $id
     */
    public static function getModulePermissions($id)
    {
        $module = UsniAdaptor::app()->getModule($id);
        if($module instanceof UiSecuredModule)
        {
            if(method_exists($module, 'getPermissionUtil'))
            {
                $permissionUtil = $module->getPermissionUtil();
                return $permissionUtil::getPermissions();
            }
        }
        return [];
    }

    /**
     * Get all permissions list.
     * @return array
     */
    public static function getAllPermissionsList()
    {
        $permissions    = AuthPermission::find()->orderBy(['resource' => SORT_ASC])->all();
        $data           = CacheUtil::get('allPermissionsList');
        if($data !== false)
        {
            return $data;
        }
        $data = [];
        foreach($permissions as $permission)
        {
            $data[$permission->module][$permission->resource][$permission->name] = $permission->alias;
        }
        CacheUtil::set('allPermissionsList', $data);
        return $data;
    }

    /**
     * Get user groups.
     * @param User $user
     */
    public static function getUserGroupNames($user)
    {
        $groups = self::getUserGroups($user->id, get_class($user));
        return array_values($groups);
    }

    /**
     * Checks if user is in admnistrative group.
     * @param User $user
     * @return boolean
     */
    public static function isUserInAdministrativeGroup(User $user)
    {
        return self::isUserInGroup($user, Group::getAdminGroupTitle());
    }

    /**
     * Checks if user is in input group.
     * @param User $user
     * @return boolean
     */
    public static function isUserInGroup(User $user, $group)
    {
        $groups = AuthManager::getUserGroupNames($user);
        if(in_array($group, $groups))
        {
            return true;
        }
        return false;
    }

    /**
     * Gets user groups.
     * @param int $userId
     * @param string $modelClassName
     * @return array
     */
    public static function getUserGroups($userId, $modelClassName)
    {
        $memberType         = strtolower(UsniAdaptor::getObjectClassName($modelClassName));
        $groupTable         = UsniAdaptor::tablePrefix() . 'group';
        $groupTrTable       = UsniAdaptor::tablePrefix() . 'group_translated';
        $groupMemberTable   = UsniAdaptor::tablePrefix() . 'group_member';
        $language           = UsniAdaptor::app()->languageManager->getContentLanguage();
        
        $sql        = "SELECT tg.id, tgt.name FROM $groupTable tg, $groupTrTable tgt, $groupMemberTable tgm
                      WHERE tgm.member_id = :mid AND tgm.member_type = :mt AND tgm.group_id = tg.id AND tg.id = tgt.owner_id AND tgt.language = :lang";
        $params     = [':mid' => $userId, ':mt' => $memberType, ':lang' => $language];
        $records    = UsniAdaptor::app()->db->createCommand($sql, $params)->queryAll();
        $groups     = [];
        foreach($records as $record)
        {
            $groups[$record['id']] = $record['name'];
        }
        return $groups;
    }
    
    /**
     * Delete auth assignment by permission
     * @param string $permission
     * @param string $authType
     * @param string $authName
     * @return void
     */
    public static function deleteAuthAssignmentsByPermission($permission, $authType, $authName)
    {
        AuthAssignment::deleteAll('identity_name = :aon AND identity_type = :aot AND permission = :pm',
                                  [':aon' => $authName, ':aot' => $authType, ':pm' => $permission]);
    }

    /**
     * Add auth assignment.
     * @param string $permission
     * @param string $authType
     * @param string $authName
     * @return void
     */
    public static function addAuthAssignment($permission, $authType, $authName)
    {
        $user                   = UsniAdaptor::app()->user->getUserModel();
        static::deleteAuthAssignmentsByPermission($permission, $authType, $authName);
        $tableName              = UsniAdaptor::app()->db->tablePrefix. 'auth_assignment';
        $authPermission         = static::getPermissionByName($permission);
        $data['identity_type']  = $authType;
        $data['identity_name']  = $authName;
        $data['permission']     = $permission;
        $data['resource']       = $authPermission['resource'];
        $data['module']         = $authPermission['module'];
        $data['created_by']     = $user->id;
        $data['created_datetime']     = date('Y-m-d H:i:s');
        UsniAdaptor::app()->db->createCommand()->insert($tableName, $data)->execute();
    }
    
    /**
     * Get permission by name
     * @param string $name
     * @return array
     */
    public static function getPermissionByName($name)
    {
        $tableName  = UsniAdaptor::app()->db->tablePrefix . 'auth_permission';
        $sql        = "SELECT * FROM $tableName WHERE name = :name";
        $dependency = new DbDependency(['sql' => "SELECT MAX(modified_datetime) FROM $tableName"]);
        return UsniAdaptor::app()->db->createCommand($sql, [':name' => $name])->cache(0, $dependency)->queryOne();
    }

    /**
     * Get auth assignments records by permission.
     * @param string $identityName
     * @param string $identityType
     * @param string $name $permission
     * @return array
     */
    public static function getAuthAssignmentsByPermission($permission, $identityType, $identityName = null, $columns = '*')
    {
        if($identityName == null)
        {
            $criteria = 'identity_type = :aot AND permission = :pm';
            $params   = [':aot' => $identityType, ':pm'  => $permission];
        }
        else
        {
            $criteria = 'identity_type = :aot AND identity_name = :aon AND permission = :pm';
            $params   = [':aot' => $identityType, ':aon' => $identityName, ':pm'  => $permission];
        }
        return AuthAssignment::find()->where($criteria, $params)->all();
    }

    /**
     * Is logged in user a super user
     * @return boolean
     */
    public static function isSuperUser($userModel)
    {
        $className = UsniAdaptor::getObjectClassName($userModel);
        if($userModel != null && $userModel->id == User::SUPER_USER_ID && $className == 'User')
        {
            return true;
        }
        return false;
    }

    /**
     * Check access for a user against permission.
     * @param Model $user
     * @param string $permission
     * @return boolean
     */
    public static function checkAccess($user, $permission)
    {
        if(AuthManager::isSuperUser($user))
        {
            return true;
        }
        $userAuthPermissions = UsniAdaptor::app()->user->getUserPermissions();
        if(in_array($permission, $userAuthPermissions))
        {
            return true;
        }
        return false;
    }

    /**
     * Checks if user belongs to allowed groups or not.
     * @param User $user
     * @param array $allowedGroups
     * @return boolean
     */
    public static function doesUserBelongToAllowedGroups($user, $allowedGroups)
    {
        if(AuthManager::isSuperUser($user))
        {
            return true;
        }
        $groupNames   = AuthManager::getUserGroupNames($user);
        $commonGroups = array_intersect($groupNames, $allowedGroups);
        if(!empty($commonGroups))
        {
            return true;
        }
        return false;
    }

    /**
     * Gets auth assignment criteria.
     * @param string $authType
     * @param string $authName
     * @return array
     */
    public static function getAssignmentCriteriaByAuthIdentity($authType, $authName)
    {
        $condition = 'identity_name = :iname AND identity_type = :itype';
        $params    = [':itype'    => $authType,
                      ':iname'    => $authName];
        return [$condition, $params];
    }
    
    /**
     * Get module permission count
     * @param string $id
     * @return int
     */
    public static function getModulePermissionCount($id)
    {
        $data = static::getConsolidatedModulePermission($id);
        return count($data);
    }
    
    /**
     * Get consolidated module permission
     * The permissions are like 
     * [auth => AuthModule' => [], 'Group' => []] so we consolidate all the permissions
     * @param string $id
     * @return int
     */
    public static function getConsolidatedModulePermission($id)
    {
        $data = [];
        $permissions = static::getModulePermissions($id);
        foreach($permissions as $resource => $set)
        {
            $data = ArrayUtil::merge($data, $set);
        }
        return $data;
    }
}