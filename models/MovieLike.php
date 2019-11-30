<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "movie_like".
 *
 * @property int $movie_id
 * @property int $user_id
 *
 * @property Movie $movie
 * @property User $user
 */
class MovieLike extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movie_like';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['movie_id', 'user_id'], 'required'],
            [['movie_id', 'user_id'], 'integer'],
            [['movie_id'], 'exist', 'skipOnError' => true, 'targetClass' => Movie::className(), 'targetAttribute' => ['movie_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'movie_id' => 'Movie ID',
            'user_id' => 'User ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMovie()
    {
        return $this->hasOne(Movie::className(), ['id' => 'movie_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
