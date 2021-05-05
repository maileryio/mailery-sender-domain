<?php

namespace Mailery\Sender\Domain\Form;

use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Form\HtmlOptions\HasLengthHtmlOptions;
use Mailery\Sender\Domain\Entity\Domain;

class DomainForm extends FormModel
{
    /**
     * @var string|null
     */
    private ?string $domain = null;

    /**
     * @param Domain $domain
     * @return self
     */
    public function withEntity(Domain $domain): self
    {
        $new = clone $this;
        $new->domain = $domain->getDomain();

        return $new;
    }

    /**
     * @return array
     */
    public function getAttributeLabels(): array
    {
        return [
            'domain' => 'Domain',
        ];
    }

    /**
     * @return array
     */
    public function getAttributeHints(): array
    {
        return [
            'domain' => 'Add a sending domain you wish to validate (yourcompany.com for example). A few simple DNS configurations are required in order for your emails to be sent directly from your domain.',
        ];
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'domain' => [
                new HasLengthHtmlOptions((new HasLength())->max(255)),
                new Callback(static function ($value) {
                    $result = new Result();

                    if (empty($value)) {
                        return $result;
                    }

                    if (filter_var('http://' . $value, FILTER_VALIDATE_URL) === false
                        || filter_var(gethostbyname($value), FILTER_VALIDATE_IP) === false
                    ) {
                         $result->addError('Domain invalid or not exists.');
                    }

                    return $result;
                })
            ],
        ];
    }
}
