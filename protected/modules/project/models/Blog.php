<?php

/**
 * Project
 *
 * Модель для работы с блогами
 *
 * @author yupe team <team@yupe.ru>
 * @link http://yupe.ru
 * @copyright 2009-2013 amyLabs && Yupe! team
 * @package yupe.modules.project.models
 * @since 0.1
 *
 */

/**
 * This is the model class for table "project".
 *
 * The followings are the available columns in table 'project':
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $icon
 * @property string $slug
 * @property integer $type
 * @property integer $status
 * @property string  $create_user_id
 * @property string  $update_user_id
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $category_id
 * @property string  $lang
 * @property integer $member_status
 * @property integer $post_status
 *
 * The followings are the available model relations:
 * @property User $createUser
 * @property User $updateUser
 * @property Post[] $posts
 */
class Project extends yupe\models\YModel
{
    /**
     *
     */
    const STATUS_BLOCKED = 0;
    /**
     *
     */
    const STATUS_ACTIVE = 1;
    /**
     *
     */
    const STATUS_DELETED = 2;

    /**
     *
     */
    const TYPE_PUBLIC = 1;
    /**
     *
     */
    const TYPE_PRIVATE = 2;

    /**
     * Returns the static model of the specified AR class.
     * @param  string $className
     * @return Project   the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{project_project}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['name, description, slug', 'required', 'except' => 'search'],
            ['name, description, slug', 'required', 'on' => ['update', 'insert']],
            [
                'type, status, create_user_id, update_user_id, create_time, update_time, category_id, member_status, post_status',
                'numerical',
                'integerOnly' => true
            ],
            ['name, icon', 'length', 'max' => 250],
            ['slug', 'length', 'max' => 150],
            ['lang', 'length', 'max' => 2],
            ['create_user_id, update_user_id, create_time, update_time, status', 'length', 'max' => 11],
            [
                'slug',
                'yupe\components\validators\YSLugValidator',
                'message' => Yii::t('ProjectModule.project', 'Illegal characters in {attribute}')
            ],
            ['type', 'in', 'range' => array_keys($this->getTypeList())],
            ['status', 'in', 'range' => array_keys($this->getStatusList())],
            ['member_status', 'in', 'range' => array_keys($this->getMemberStatusList())],
            ['post_status', 'in', 'range' => array_keys($this->getPostStatusList())],
            ['name, slug, description', 'filter', 'filter' => [new CHtmlPurifier(), 'purify']],
            ['slug', 'unique'],
            [
                'id, name, description, slug, type, status, create_user_id, update_user_id, create_time, update_time, lang, category_id',
                'safe',
                'on' => 'search'
            ],
        ];
    }

    /**
     * @return array
     */
    public function getPostStatusList()
    {
        return Post::model()->getStatusList();
    }

    /**
     * @return array
     */
    public function getMemberStatusList()
    {
        return UserToProject::model()->getStatusList();
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return [
            'createUser'   => [self::BELONGS_TO, 'User', 'create_user_id'],
            'updateUser'   => [self::BELONGS_TO, 'User', 'update_user_id'],
            'posts'        => [
                self::HAS_MANY,
                'Post',
                'project_id',
                'condition' => 't.status = :status',
                'params'    => [':status' => Post::STATUS_PUBLISHED]
            ],
            'userToProject'   => [
                self::HAS_MANY,
                'UserToProject',
                'project_id',
                'joinType'  => 'left join',
                'condition' => 'userToProject.status = :status',
                'params'    => [':status' => UserToProject::STATUS_ACTIVE]
            ],
            'members'      => [self::HAS_MANY, 'User', ['user_id' => 'id'], 'through' => 'userToProject'],
            'postsCount'   => [
                self::STAT,
                'Post',
                'project_id',
                'condition' => 't.status = :status',
                'params'    => [':status' => Post::STATUS_PUBLISHED]
            ],
            'membersCount' => [
                self::STAT,
                'UserToProject',
                'project_id',
                'condition' => 'status = :status',
                'params'    => [':status' => UserToProject::STATUS_ACTIVE]
            ],
            'category'     => [self::BELONGS_TO, 'Category', 'category_id']
        ];
    }

    /**
     * @return array
     */
    public function scopes()
    {
        return [
            'published' => [
                'condition' => 't.status = :status',
                'params'    => [':status' => self::STATUS_ACTIVE],
            ],
            'public'    => [
                'condition' => 'type = :type',
                'params'    => [':type' => self::TYPE_PUBLIC],
            ],
        ];
    }

    /**
     * Условие для получения блога по url
     *
     * @param string $url - url данного блога
     *
     * @return self
     */
    public function getByUrl($url = null)
    {
        $this->getDbCriteria()->mergeWith(
            [
                'condition' => $this->getTableAlias() . '.slug = :slug',
                'params'    => [
                    ':slug' => $url,
                ],
            ]
        );

        return $this;
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'id'             => Yii::t('ProjectModule.project', 'id'),
            'name'           => Yii::t('ProjectModule.project', 'Title'),
            'description'    => Yii::t('ProjectModule.project', 'Description'),
            'icon'           => Yii::t('ProjectModule.project', 'Icon'),
            'slug'           => Yii::t('ProjectModule.project', 'URL'),
            'type'           => Yii::t('ProjectModule.project', 'Type'),
            'status'         => Yii::t('ProjectModule.project', 'Status'),
            'create_user_id' => Yii::t('ProjectModule.project', 'Created'),
            'update_user_id' => Yii::t('ProjectModule.project', 'Updated'),
            'create_time'    => Yii::t('ProjectModule.project', 'Created at'),
            'update_time'    => Yii::t('ProjectModule.project', 'Updated at'),
            'category_id'    => Yii::t('ProjectModule.project', 'Category'),
            'member_status'  => Yii::t('ProjectModule.project', 'User status'),
            'post_status'    => Yii::t('ProjectModule.project', 'Post status'),
        ];
    }

    /**
     * @return array customized attribute descriptions (name=>description)
     */
    public function attributeDescriptions()
    {
        return [
            'id'          => Yii::t('ProjectModule.project', 'Post id.'),
            'name'        => Yii::t(
                'ProjectModule.project',
                'Please enter a title of the project. For example: <span class="label label-default">My travel notes</span>.'
            ),
            'description' => Yii::t(
                'ProjectModule.project',
                'Please enter a short description of the project. For example:<br /><br /> <pre>Notes on my travel there and back again. Illustrated.</pre>'
            ),
            'icon'        => Yii::t('ProjectModule.project', 'Please choose an icon for the project.'),
            'slug'        => Yii::t(
                'ProjectModule.project',
                'Please enter an URL-friendly name for the project.<br /><br /> For example: <pre>http://site.ru/projects/<span class="label label-default">travel-notes</span>/</pre> If you don\'t know how to fill this field you can leave it empty.'
            ),
            'type'        => Yii::t(
                'ProjectModule.project',
                'Please choose a type of the project:<br /><br /><span class="label label-success">public</span> &ndash; anyone can create posts<br /><br /><span class="label label-info">private</span> &ndash; only you can create posts'
            ),
            'status'      => Yii::t(
                'ProjectModule.project',
                'Please choose a status of the project:<br /><br /><span class="label label-success">active</span> &ndash; The project will be visible and it will be possible to create new records<br /><br /><span class="label label-warning">blocked</span> &ndash; The project will be visible but it would not be possible to create new records<br /><br /><span class="label label-danger">removed</span> &ndash; The project will be invisible'
            ),
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models
     *                             based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria();

        $criteria->select = 't.*, count(post.id) as postsCount, count(member.id) as membersCount';
        $criteria->join = ' LEFT JOIN {{project_post}} post ON post.project_id = t.id
                            LEFT JOIN {{project_user_to_project}} member ON member.project_id = t.id';
        $criteria->group = 't.id';

        $criteria->compare('t.id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('slug', $this->slug, true);
        $criteria->compare('type', $this->type);
        $criteria->compare('t.status', $this->status);
        $criteria->compare('t.create_user_id', $this->create_user_id);
        $criteria->compare('t.update_user_id', $this->update_user_id);
        $criteria->compare('category_id', $this->category_id);
        $criteria->compare('create_time', $this->create_time);
        $criteria->compare('update_time', $this->update_time);

        $criteria->with = ['createUser', 'updateUser', 'postsCount', 'membersCount'];

        $sort = new CSort();
        $sort->defaultOrder = [
            'postsCount' => CSort::SORT_DESC
        ];

        $sort->attributes = [
            'postsCount'   => [
                'asc'   => 'postsCount ASC',
                'desc'  => 'postsCount DESC',
                'label' => Yii::t('ProjectModule.project', 'Posts count')
            ],
            'membersCount' => [
                'asc'   => 'membersCount ASC',
                'desc'  => 'membersCount DESC',
                'label' => Yii::t('ProjectModule.project', 'Members count')
            ],
            '*', // add all of the other columns as sortable
        ];

        return new CActiveDataProvider(get_class($this), [
            'criteria'   => $criteria,
            'pagination' => ['pageSize' => 10],
            'sort'       => $sort
        ]);
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $module = Yii::app()->getModule('project');

        return [
            'imageUpload'        => [
                'class'         => 'yupe\components\behaviors\ImageUploadBehavior',
                'attributeName' => 'icon',
                'minSize'       => $module->minSize,
                'maxSize'       => $module->maxSize,
                'types'         => $module->allowedExtensions,
                'uploadPath'    => $module->uploadPath,
                'defaultImage'  => Yii::app()->getTheme()->getAssetsUrl() . '/images/project-default.jpg',
            ],
            'CTimestampBehavior' => [
                'class'             => 'zii.behaviors.CTimestampBehavior',
                'setUpdateOnCreate' => true,
            ],
            'seo'                => [
                'class'  => 'vendor.chemezov.yii-seo.behaviors.SeoActiveRecordBehavior',
                'route'  => '/project/project/view',
                'params' => [
                    'slug' => function ($data) {
                        return $data->slug;
                    }
                ],
            ],
        ];
    }

    /**
     * @return bool
     */
    public function beforeSave()
    {
        $this->update_user_id = Yii::app()->getUser()->getId();

        if ($this->isNewRecord) {
            $this->create_user_id = $this->update_user_id;
        }

        return parent::beforeSave();
    }

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if (!$this->slug) {
            $this->slug = yupe\helpers\YText::translit($this->name);
        }

        return parent::beforeValidate();
    }

    /**
     *
     */
    public function afterDelete()
    {
        Comment::model()->deleteAll(
            'model = :model AND model_id = :model_id',
            [
                ':model'    => 'Project',
                ':model_id' => $this->id
            ]
        );

        return parent::afterDelete();
    }

    /**
     * @return array
     */
    public function getStatusList()
    {
        return [
            self::STATUS_BLOCKED => Yii::t('ProjectModule.project', 'Blocked'),
            self::STATUS_ACTIVE  => Yii::t('ProjectModule.project', 'Active'),
            self::STATUS_DELETED => Yii::t('ProjectModule.project', 'Removed'),
        ];
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $data = $this->getStatusList();

        return isset($data[$this->status]) ? $data[$this->status] : Yii::t('ProjectModule.project', '*unknown*');
    }

    /**
     * @return array
     */
    public function getTypeList()
    {
        return [
            self::TYPE_PUBLIC  => Yii::t('ProjectModule.project', 'Public'),
            self::TYPE_PRIVATE => Yii::t('ProjectModule.project', 'Private'),
        ];
    }

    /**
     * @return string
     */
    public function getType()
    {
        $data = $this->getTypeList();

        return isset($data[$this->type]) ? $data[$this->type] : Yii::t('ProjectModule.project', '*unknown*');
    }

    /**
     * @param $userId
     * @param int $status
     * @return bool|int
     */
    public function userIn($userId, $status = UserToProject::STATUS_ACTIVE)
    {
        $projects = Yii::app()->getCache()->get("Project::Project::members::{$userId}");

        if (false === $projects) {

            $result = Yii::app()->getDb()->createCommand(
                'SELECT project_id, status FROM {{project_user_to_project}} WHERE user_id = :userId'
            )->bindValue(':userId', (int)$userId)
                ->queryAll();

            $projects = [];

            foreach ($result as $data) {
                $projects[$data['project_id']] = $data['status'];
            }

            Yii::app()->getCache()->set("Project::Project::members::{$userId}", $projects);
        }

        if (false !== $status) {
            if (isset($projects[$this->id]) && (int)$projects[$this->id] === (int)$status) {
                return true;
            }

            return false;
        }

        return isset($projects[$this->id]) ? (int)$projects[$this->id] : false;
    }

    /**
     * @param $userId
     * @return CActiveRecord
     */
    public function getUserMembership($userId)
    {
        return UserToProject::model()->find(
            'user_id = :userId AND project_id = :projectId',
            [
                ':userId' => (int)$userId,
                ':projectId' => $this->id
            ]
        );
    }

    /**
     * @param $userId
     * @param $status
     * @return mixed
     */
    public function hasUserInStatus($userId, $status)
    {
        return Yii::app()->getDb()->createCommand(
            'SELECT count(id)
                FROM {{project_user_to_project}}
                 WHERE user_id = :userId AND project_id = :projectId AND status = :status'
        )
            ->bindValue(':userId', (int)$userId)
            ->bindValue(':status', (int)$status)
            ->bindValue(':projectId', $this->id)
            ->queryScalar();
    }

    /**
     * @param $userId
     * @return bool
     */
    public function join($userId)
    {
        if ($this->isPrivate()) {
            return false;
        }

        if ($this->userIn((int)$userId)) {
            return true;
        }

        //check user status in project
        $member = $this->getUserMembership($userId);

        if (null === $member) {

            $member = new UserToProject();
            $member->project_id = $this->id;
            $member->user_id = (int)$userId;
            $member->status = (int)$this->member_status;

        } else {

            if ($member->isDeleted()) {
                $member->activate();
            } else {
                return false;
            }
        }

        if ($member->save()) {

            Yii::app()->eventManager->fire(ProjectEvents::BLOG_JOIN, new ProjectJoinLeaveEvent($this, $userId));

            Yii::app()->getCache()->delete("Project::Project::members::{$userId}");

            return true;
        }

        return false;
    }

    /**
     * @param $userId
     * @return bool|int
     */
    public function leave($userId)
    {
        if ($this->isPrivate()) {
            return false;
        }

        Yii::app()->getCache()->delete("Project::Project::members::{$userId}");

        Yii::app()->eventManager->fire(ProjectEvents::BLOG_LEAVE, new ProjectJoinLeaveEvent($this, $userId));

        return UserToProject::model()->updateAll(
            [
                'status'      => UserToProject::STATUS_DELETED,
                'update_time' => new CDbExpression('NOW()')
            ],
            'user_id = :userId AND project_id = :projectId',
            [
                ':userId' => (int)$userId,
                ':projectId' => $this->id
            ]
        );
    }

    /**
     * @return UserToProject
     */
    public function getMembersList()
    {
        $members = new UserToProject('search');
        $members->unsetAttributes();
        $members->project_id = $this->id;
        $members->status = UserToProject::STATUS_ACTIVE;

        return $members;
    }

    /**
     * @return Post
     */
    public function getPosts()
    {
        $posts = new Post('search');
        $posts->unsetAttributes();
        $posts->project_id = $this->id;
        $posts->status = Post::STATUS_PUBLISHED;
        $posts->access_type = Post::ACCESS_PUBLIC;

        return $posts;
    }

    /**
     * @return mixed
     */
    public function getList()
    {
        return $this->published()->findAll(['order' => 'name ASC']);
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getMembershipListForUser($user)
    {
        return $this->with('userToProject')->published()->findAll(
            [
                'condition' => '(userToProject.user_id = :userId AND userToProject.status = :status)',
                'params'    => [
                    ':status' => UserToProject::STATUS_ACTIVE,
                    ':userId' => (int)$user
                ],
                'order'     => 'name ASC'
            ]
        );
    }

    /**
     * @param $id
     * @param array $with
     * @return mixed
     */
    public function get($id, array $with = ['posts', 'membersCount', 'createUser'])
    {
        return $this->with($with)->published()->findByPk((int)$id);
    }

    /**
     * @param $id
     * @param array $with
     * @return mixed
     */
    public function getBySlug($id, array $with = ['posts', 'membersCount', 'createUser'])
    {
        return $this->with($with)->getByUrl($id)->published()->find();
    }

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->type == self::TYPE_PRIVATE;
    }

    /**
     * @param $userId
     * @return bool
     */
    public function isOwner($userId)
    {
        return $this->create_user_id == $userId;
    }

    public function getPrivateProjectsForUser($userId)
    {
        return $this->published()->findAll('create_user_id = :id AND type = :type', [
            ':id'   => (int)$userId,
            ':type' => self::TYPE_PRIVATE
        ]);
    }

    public function getListForUser($userId)
    {
        return CMap::mergeArray($this->getMembershipListForUser($userId), $this->getPrivateProjectsForUser($userId));
    }

}
