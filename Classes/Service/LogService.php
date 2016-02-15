<?php
namespace Serfhos\MyUserManagement\Service;

/**
 * Service: Logs
 *
 * @package Serfhos\MyUserManagement\Service
 */
class LogService implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * Default static values for readability
     */
    const TYPE_LOGGED_IN = 255;
    const ACTION_LOG_IN = 1;

    /**
     * Find login actions from sys_log
     *
     * @param array $parameters
     * @return array
     */
    public function findUserLoginActions($parameters = null)
    {
        $whereParts = array(
            'userId' => '`userId` > 0',
            'tstamp' => '`tstamp` > 0',
            'level' => '`level` = 0',
            'type' => '`type` = ' . self::TYPE_LOGGED_IN,
            'action' => '`action` = ' . self::ACTION_LOG_IN,
        );

        // Set parameter query parts
        if (!empty($parameters)) {
            if ($parameters['user'] !== null) {
                $whereParts['userId'] = '`userId` = ' . (int) $parameters['user'];
            }
        }

        // Set default variables for output
        $logs = array(
            'information' => array(
                'total' => $this->getDatabaseConnection()->exec_SELECTcountRows('uid', 'sys_log', implode(' AND ', $whereParts)),
                'itemsPerPage' => null,
                'page' => 1,
            ),
            'items' => array(),
        );
        $logs['information']['itemsPerPage'] = $logs['information']['total'];

        // Apply pagination
        $limit = null;
        if ((!empty($parameters)) && $parameters['itemsPerPage'] !== null) {
            $logs['information']['itemsPerPage'] = (int) $parameters['itemsPerPage'];

            $limit = array(
                'offset' => 0,
                'items' => (int) $parameters['itemsPerPage'],
            );
            if ($parameters['page'] !== null) {
                $logs['information']['page'] = (int) $parameters['page'];
                $limit['offset'] = $limit['items'] * ((int) $parameters['page'] - 1);
            }
        }

        // Render all requested logs
        $res = $this->getDatabaseConnection()->exec_SELECTquery(
            '*',
            'sys_log',
            implode(' AND ', $whereParts),
            '',
            'tstamp DESC',
            ($limit ? implode(', ', $limit) : null)
        );
        while ($row = $this->getDatabaseConnection()->sql_fetch_assoc($res)) {
            $data = unserialize($row['log_data']);
            $logs['items'][] = array(
                'user_id' => $row['userid'],
                'user_login' => $data[0],
                'user_ip' => $row['IP'],
                'tstamp' => $row['tstamp'],
            );
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