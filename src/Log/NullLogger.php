<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Log;

class NullLogger implements LoggerInterface
{
    /**
     * @param string $logMessage
     *
     * @return void
     */
    public function warning($logMessage)
    {
        // NOP
    }

    /**
     * @param string $logMessage
     *
     * @return void
     */
    public function error($logMessage)
    {
        // NOP
    }

    /**
     * @param string $logMessage
     *
     * @return void
     */
    public function notice($logMessage)
    {
        // NOP
    }

    /**
     * @param string $logMessage
     *
     * @return void
     */
    public function info($logMessage)
    {
        // NOP
    }
}
