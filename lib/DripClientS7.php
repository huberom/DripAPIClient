<?php

namespace HOMC;

use HOMC\DripClient;

class DripClientS7 extends DripClient
{
    const TAG_BUNDLE_INSTALL  = 'S7 Bundle Install';
    const TAG_TIMER_INSTALL   = 'S7 Timer Install';
    const TAG_EXPRESS_INSTALL = 'S7 Express Install';
    
    const TAG_BUNDLE_UNINSTALL  = 'S7 Bundle Un-Install';
    const TAG_TIMER_UNINSTALL   = 'S7 Timer Un-Install';
    const TAG_EXPRESS_UNINSTALL = 'S7 Express Un-Install';
}