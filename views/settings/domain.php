<?php declare(strict_types=1);

use Mailery\Web\Widget\FlashMessage;
use Mailery\Sender\Domain\Entity\DnsRecord;
use Yiisoft\Form\Widget\Form;
use Yiisoft\Yii\Widgets\ContentDecorator;

/** @var Yiisoft\Yii\WebView $this */
/** @var Psr\Http\Message\ServerRequestInterface $request */
/** @var Mailery\Brand\Entity\Brand $brand */
/** @var Yiisoft\Form\FormModelInterface $form */
/** @var Mailery\Sender\Domain\Entity\Domain $domain */
/** @var Yiisoft\Yii\View\Csrf $csrf */
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
                ->action($url->generate('/brand/settings/domain'))
                ->csrf($csrf)
                ->id('sender-domain-form')
                ->begin(); ?>

        <h3 class="h6">Sending domain</h3>
        <div class="mb-4"></div>

        <?= $field->text($form, 'domain')->autofocus(); ?>

        <?= $field->submitButton()
                ->class('btn btn-primary float-right mt-2')
                ->value('Save'); ?>

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
                <?php
                    $dnsRecords = $domain->getDnsRecords()->toArray();
                    usort(
                        $dnsRecords,
                        function (DnsRecord $a, DnsRecord $b) {
                            return $a->getId() < $b->getId() ? -1 : 1;
                        }
                    );

                    foreach($dnsRecords as $index => $dnsRecord) {
                    /** @var DnsRecord $dnsRecord */
                    ?><b-card no-body class="mb-1">
                        <b-card-header header-tag="header" class="p-1" role="tab">
                            <b-button v-b-toggle.check-dns-<?= $index ?>>
                                <?= $dnsRecord->getSubType()->getLabel() ?>
                            </b-button>
                            <?= '<span class="ml-2 badge ' . $dnsRecord->getStatus()->getCssClass() . '">' . $dnsRecord->getStatus()->getLabel() . '</span>'; ?>
                        </b-card-header>
                        <b-collapse id="check-dns-<?= $index ?>" accordion="check-dns" role="tabpanel">
                            <b-card-body>
                                <b-card-text>
                                    <div class="p-1">Type</div>
                                    <div class="bg-light border p-2"><?= $dnsRecord->getType() ?></div>

                                    <div class="p-2"></div>

                                    <div class="p-1">Name</div>
                                    <div class="bg-light border p-2"><?= $dnsRecord->getName() ?></div>

                                    <?php if (!$dnsRecord->getType()->isMx()) { ?>
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
                ->action($url->generate('/brand/settings/check-dns'))
                ->csrf($csrf)
                ->id('sender-domain-check-dns-form')
                ->begin(); ?>

            <?= $field->submitButton()
                ->class('btn btn-primary float-right mt-2')
                ->value('Check DNS records'); ?>

            <?= Form::end(); ?>
        </div>
    </div>
<?php } ?>

<?= ContentDecorator::end() ?>
