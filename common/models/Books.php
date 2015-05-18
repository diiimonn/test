<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "books".
 *
 * @property integer $id
 * @property integer $author_id
 * @property string $name
 * @property integer $date
 * @property string $preview
 * @property integer $date_create
 * @property integer $date_update
 *
 * @property Authors $author
 */
class Books extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'date_create',
                'updatedAtAttribute' => 'date_update'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'books';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author_id', 'name'], 'required'],
            [['author_id', 'date_create', 'date_update'], 'integer'],
            [['date'], 'date', 'format' => 'Y-m-d'],
            [['name'], 'string'],
            [['preview'], 'file', 'extensions' => ['jpg','gif','png']]
        ];
    }

    public function afterDelete()
    {
        parent::afterDelete();

        $pathOldInternal = Yii::getAlias('@frontend') . '/web' . $this->preview;

        if (is_file($pathOldInternal)) {
            unlink($pathOldInternal);
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'name' => 'Name',
            'date' => 'Date',
            'preview' => 'Preview',
            'date_create' => 'Date Create',
            'date_update' => 'Date Update',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(Authors::className(), ['id' => 'author_id']);
    }
}
