<?php

namespace Xgrz\InPost\Enums;

enum PointStatus: string
{
    case OPERATING = 'Operating';
    case NON_OPERATING = 'NonOperating';
    case DISABLED = 'Disabled';
}
