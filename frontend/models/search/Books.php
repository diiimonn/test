<?php

namespace frontend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Books as BooksModel;

/**
 * Books represents the model behind the search form about `common\models\Books`.
 */
class Books extends BooksModel
{
    public $date_start;
    public $date_stop;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author_id'], 'integer'],
            [['name', 'date_start', 'date_stop'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BooksModel::find();
        $query->joinWith('author');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->author_id) {
            $query->andFilterWhere([
                'author_id' => $this->author_id,
            ]);
        }


        if ($this->date_start || $this->date_stop) {
            $condition = ['and'];

            if ($this->date_start) {
                $condition[] = ['>=', 'date', date('Y-m-d', strtotime(preg_replace('~\D+~', '-', $this->date_start)))];
            }

            if ($this->date_stop) {
                $condition[] = ['<=', 'date', date('Y-m-d', strtotime(preg_replace('~\D+~', '-', $this->date_stop)))];
            }

            $query->andWhere($condition);
        }

        $query->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
