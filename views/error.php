<?php
use yii\helpers\Html;

$this->title = $name;
?>
<div>
    <h1>ssssssssss</h1>

    <h1><?= Html::encode($this->title) ?></h1>

    <div>
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        The above error occurred while the Web server was processing your request.
    </p>
    <p>
        Please contact us if you think this is a server error. Thank you.
    </p>

</div>
