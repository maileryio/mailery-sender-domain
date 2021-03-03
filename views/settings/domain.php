<?php declare(strict_types=1);

use Mailery\Web\Widget\FlashMessage;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Html\Html;
use Yiisoft\Yii\Widgets\ContentDecorator;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Brand\Entity\Brand $brand */
/** @var Mailery\Sender\Domain\Form\SettingsForm $form */
/** @var Mailery\Sender\Domain\Entity\Domain $domain */
/** @var string $csrf */
?>

<?= ContentDecorator::widget()
    ->viewFile('@vendor/maileryio/mailery-brand/views/settings/_layout.php')
    ->begin(); ?>

<div class="mb-5"></div>
<div class="row">
    <div class="col-12 col-xl-4">
        <?= FlashMessage::widget(); ?>
    </div>
</div>

<div class="row">
    <div class="col-12 col-xl-4">
        <?= Form::widget()
            ->action($urlGenerator->generate('/brand/settings/domain'))
            ->options(
                [
                    'id' => 'form-brand',
                    'csrf' => $csrf,
                    'enctype' => 'multipart/form-data',
                ]
            )
            ->begin(); ?>

        <h3 class="h6">Sending domain</h3>
        <div class="mb-4"></div>

        <?= $field->config($form, 'domain'); ?>

        <?= Html::submitButton(
            'Save',
            [
                'class' => 'btn btn-primary float-right mt-2'
            ]
        ); ?>

        <?= Form::end(); ?>
    </div>
</div>

<?php if ($domain !== null) { ?>
    <div class="row">
        <div class="col-12 col-xl-4">
            <h3 class="h6">Domain verification</h3>
            <div class="form-text text-muted">
                To improve your sender reputation and deliverability, we strongly recommend that you set up a few DNS records.
                This will allow us to sign outgoing email using DKIM and DomainKeys, and will inform your contacts' email providers that we are allowed to send your emails.
            </div>
            <div class="mb-4"></div>

            <div class="accordion" role="tablist">
                <?php foreach($domain->getDnsRecords() as $index => $dnsRecord) {
                    /** @var Mailery\Channel\Email\Entity\DnsRecord $dnsRecord */
                    ?><b-card no-body class="mb-1">
                        <b-card-header header-tag="header" class="p-1" role="tab">
                            <b-button v-b-toggle.check-dns-<?= $index ?>>
                                <?= $dnsRecord->getSubtype() ?>
                            </b-button>
                             <?php if ($dnsRecord->isPending()) {
                                echo '<span class="ml-2 badge badge-warning">pending</span>';
                            } else if ($dnsRecord->isFound()) {
                                echo '<span class="ml-2 badge badge-success">found</span>';
                            } else if ($dnsRecord->isNotFound()) {
                                echo '<span class="ml-2 badge badge-danger">not found</span>';
                            } else {
                                echo '<span class="ml-2 badge badge-secondary">unknown</span>';
                            } ?>
                        </b-card-header>
                        <b-collapse id="check-dns-<?= $index ?>" accordion="check-dns" role="tabpanel">
                            <b-card-body>
                                <b-card-text>
                                    <div class="p-1">Type</div>
                                    <div class="bg-light border p-2"><?= $dnsRecord->getType() ?></div>

                                    <div class="p-2"></div>

                                    <div class="p-1">Name</div>
                                    <div class="bg-light border p-2"><?= $dnsRecord->getName() ?></div>

                                    <?php if (!$dnsRecord->isMx()) { ?>
                                        <div class="p-2"></div>

                                        <div class="p-1">Content</div>
                                        <div class="bg-light border p-2"><?= $dnsRecord->getContent() ?></div>
                                    <?php } ?>
                                </b-card-text>
                            </b-card-body>
                        </b-collapse>
                    </b-card><?php
                } ?>
            </div>

            <?= Form::widget()
                ->action($urlGenerator->generate('/brand/settings/check-dns'))
                ->options(
                    [
                        'id' => 'form-brand',
                        'csrf' => $csrf,
                        'enctype' => 'multipart/form-data',
                    ]
                )
                ->begin(); ?>

            <?= Html::submitButton(
                'Check DNS records',
                [
                    'class' => 'btn btn-primary float-right mt-2'
                ]
            ); ?>

            <?= Form::end(); ?>
        </div>
    </div>
<?php } ?>

<?= ContentDecorator::end() ?>
