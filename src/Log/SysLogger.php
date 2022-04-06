<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Log;

use RuntimeException;

class SysLogger implements LoggerInterface
{
    /**
     * @param string $appName
     */
    public function __construct($appName)
    {
        if (false === openlog($appName, \LOG_PERROR | \LOG_ODELAY, \LOG_USER)) {
            throw new RuntimeException('unable to open syslog');
        }
    }

    public function __destruct()
    {
        closelog();
    }

    /**
     * @param string $logMessage
     *
     * @return void
     */
    public function warning($logMessage)
    {
        syslog(\LOG_WARNING, $logMessage);
    }

    /**
     * @param string $logMessage
     *
     * @return void
     */
    public function error($logMessage)
    {
        syslog(\LOG_ERR, $logMessage);
    }

    /**
     * @param string $logMessage
     *
     * @return void
     */
    public function info($logMessage)
    {
        syslog(\LOG_INFO, $logMessage);
    }
}
