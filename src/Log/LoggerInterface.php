<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common\Log;

interface LoggerInterface
{
    /**
     * @param string $logMessage
     *
     * @return void
     */
    public function warning($logMessage);

    /**
     * @param string $logMessage
     *
     * @return void
     */
    public function error($logMessage);

    /**
     * @param string $logMessage
     *
     * @return void
     */
    public function info($logMessage);
}
