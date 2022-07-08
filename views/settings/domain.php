<?php declare(strict_types=1);

use Mailery\Web\Widget\FlashMessage;
use Mailery\Sender\Domain\Entity\DnsRecord;
use Yiisoft\Html\Tag\Form;
use Yiisoft\Yii\Widgets\ContentDecorator;
use Yiisoft\Form\Field;

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

<div class="mb-2"></div>
<div class="row">
    <div class="col-12">
        <?= FlashMessage::widget(); ?>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?= Form::tag()
                ->csrf($csrf)
                ->id('sender-domain-form')
                ->post($url->generate('/brand/settings/domain'))
                ->open(); ?>

        <h6 class="font-weight-bold">Sending domain</h6>
        <div class="mb-3"></div>

        <?= Field::text($form, 'domain')->autofocus(); ?>

        <?= Field::submitButton()
                ->content('Save changes'); ?>

        <?= Form::tag()->close(); ?>
    </div>
</div>

<?php if ($domain !== null) { ?>
    <div class="mb-4"></div>
    <div class="row">
        <div class="col-12">
            <h6 class="font-weight-bold">Domain verification</h6>
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
                    ?><b-card no-body>
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

            <?= Form::tag()
                    ->csrf($csrf)
                    ->id('sender-domain-check-dns-form')
                    ->post($url->generate('/brand/settings/check-dns'))
                    ->open(); ?>

            <?= Field::submitButton()
                    ->content('Check DNS records'); ?>

            <?= Form::tag()->close(); ?>
        </div>
    </div>
<?php } ?>

<?= ContentDecorator::end() ?>
