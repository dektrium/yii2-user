<?php


namespace dektrium\user\widgets;


use dektrium\user\models\Token;
use yii\widgets\ListView;

class RecoveryCodes extends ListView
{
    const HTML_VIEW = '/../widgets/views/recovery-codes';
    const TEXT_VIEW = '/../widgets/views/text/recovery-codes';

    /**
     * @var Token[]
     */
    public $codes;

    /**
     * @inheritdoc
     */
    public $itemView = self::HTML_VIEW;

    public $options = [
        'tag' => 'table',
        'class' => 'table text-center',
    ];

    public $itemOptions = [
        'tag' => 'tr',
        'class' => 'warning',
    ];

    public $summary = false;
}