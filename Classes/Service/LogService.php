<?php

namespace KoninklijkeCollective\MyUserManagement\Service;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * Service: Logs
 */
class LogService implements SingletonInterface
{

    /**
     * Default static values for readability
     */
    public const TYPE_LOGGED_IN = 255;
    public const ACTION_LOG_IN = 1;

    /**
     * Find login actions from sys_log
     *
     * @param  array  $parameters
     * @return array
     */
    public function findUserLoginActions($parameters = null)
    {
        $whereParts = [
            'join' => 'sys_log.`userId` = be_users.uid',
            'enabled' => '(be_users.disable = 0 AND be_users.deleted = 0)',
            'userId' => 'sys_log.`userId` > 0',
            'tstamp' => 'sys_log.`tstamp` > 0',
            'level' => 'sys_log.`level` = 0',
            'type' => 'sys_log.`type` = ' . self::TYPE_LOGGED_IN,
            'action' => 'sys_log.`action` = ' . self::ACTION_LOG_IN,
        ];

        // Set parameter query parts
        if (!empty($parameters)) {
            if ($parameters['user'] !== null) {
                $whereParts['userId'] = 'sys_log.`userId` = ' . (int)$parameters['user'];
            }
            if ((bool)$parameters['hide-admin'] === true) {
                $whereParts['hide-admin'] = 'be_users.admin = 0';
            }
        }

        // Set default variables for output
        $logs = [
            'information' => [
                'total' => $this->getDatabaseConnection()
                    ->exec_SELECTcountRows('sys_log.uid', 'sys_log, be_users', implode(' AND ', $whereParts)),
                'itemsPerPage' => null,
                'page' => 1,
            ],
            'items' => [],
        ];
        $logs['information']['itemsPerPage'] = $logs['information']['total'];

        // Apply pagination
        $limit = null;
        if ((!empty($parameters)) && $parameters['itemsPerPage'] !== null) {
            $logs['information']['itemsPerPage'] = (int)$parameters['itemsPerPage'];

            $limit = [
                'offset' => 0,
                'items' => (int)$parameters['itemsPerPage'],
            ];
            if ($parameters['page'] !== null) {
                $logs['information']['page'] = (int)$parameters['page'];
                $limit['offset'] = $limit['items'] * ((int)$parameters['page'] - 1);
            }
        }

        // Render all requested logs
        $res = $this->getDatabaseConnection()->exec_SELECTquery(
            'sys_log.*',
            'sys_log, be_users',
            implode(' AND ', $whereParts),
            '',
            'sys_log.tstamp DESC',
            ($limit ? implode(', ', $limit) : null)
        );
        while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($res)) {
            $data = unserialize($row['log_data']);
            $logs['items'][] = [
                'user_id' => $row['userid'],
                'user_login' => $data[0],
                'user_ip' => $row['IP'],
                'tstamp' => $row['tstamp'],
            ];
        }

        return $logs;
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
