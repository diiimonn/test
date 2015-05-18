<?php

use yii\helpers\Html;
use yii\grid\GridView;
use dosamigos\datepicker\DatePicker;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\search\Books */
/* @var $dataProvider yii\data\ActiveDataProvider */
/**
 * @var \common\models\Authors[] $modelsAuthors
 */

$this->title = Yii::t('app', 'Books');
$this->params['breadcrumbs'][] = $this->title;

$authors = [Yii::t('app', 'Authors')] + ArrayHelper::map($modelsAuthors, 'id', function($m) {
    /** @var \common\models\Authors $m */

    return $m->firstname . ' ' . $m->lastname;
});
?>

<?php $form = ActiveForm::begin([
    'options' => [
        'class' => 'form-horizontal'
    ]
]) ?>
<div class="row" style="margin-bottom: 20px;">
    <div class="col-lg-4">
        <?= Html::activeDropDownList($searchModel, 'author_id', $authors, [
            'class' => 'form-control'
        ]) ?>
    </div>
    <div class="col-lg-4">
        <?= Html::activeTextInput($searchModel, 'name', [
            'class' => 'form-control',
            'placeholder' => Yii::t('app', 'Book name')
        ]) ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="row">
            <div class="col-lg-7">
                <div class="form-group">
                    <label for="books-date" class="col-lg-6 control-label">
                        <?= $searchModel->getAttributeLabel('date') ?>:
                    </label>
                    <div class="col-lg-6">
                        <?= DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'date_start',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'dd/mm/yyyy'
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="form-group">
                    <label for="books-date" class="col-lg-2 control-label">
                        <?= Yii::t('app', 'To') ?>:
                    </label>
                    <div class="col-lg-9">
                        <?= DatePicker::widget([
                            'model' => $searchModel,
                            'attribute' => 'date_stop',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'dd/mm/yyyy'
                            ]
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <?= Html::submitInput(Yii::t('app', 'Search'), [
            'class' => 'btn btn-default pull-right'
        ]) ?>
    </div>
</div>

<?php ActiveForm::end() ?>

<div class="books-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterPosition' => '',
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'name:ntext',
            [
                'attribute' => 'preview',
                'format' => 'raw',
                'value' => function($model){
                        /** @var \common\models\Books $model */

                        return Html::tag('div', Html::img($model->preview, [
                            'style' => 'max-width: 100px; max-height: 100px;'
                        ]), [
                            'style' => 'text-align: center;'
                        ]);
                    }
            ],
            [
                'attribute' => 'author_id',
                'format' => 'raw',
                'value' => function($model){
                        /** @var \common\models\Books $model */

                        return $model->author->firstname . ' ' . $model->author->lastname;
                    }
            ],
            [
                'attribute' => 'date',
                'format' => 'raw',
                'value' => function($model){
                        /** @var \common\models\Books $model */

                        return Yii::$app->formatter->asDate($model->date, 'd MMMM yyyy');
                    }
            ],
            [
                'attribute' => 'date_create',
                'format' => 'raw',
                'value' => function($model){
                        /** @var \common\models\Books $model */

                        return date('d-m-Y', $model->date_create);
                    }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
