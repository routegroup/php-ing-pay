<?php

declare(strict_types=1);

namespace Routegroup\IngPay\Types;

/** Only meaningful for the oneclick and recurring channels. */
enum ProfileType: string
{
    case FIRST = 'FIRST';
    case STANDARD = 'STANDARD';
}
