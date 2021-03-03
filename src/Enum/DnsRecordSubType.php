<?php

namespace Mailery\Sender\Domain\Enum;

class DnsRecordSubType
{
    public const SPF = 'SPF';
    public const MX = 'MX';
    public const DKIM = 'DKIM';
    public const DMARC = 'DMARC';
}