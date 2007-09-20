<?php

/*
+---------------------------------------------------------------------------+
| Openads v${RELEASE_MAJOR_MINOR}                                           |
| ============                                                              |
|                                                                           |
| Copyright (c) 2003-2007 Openads Limited                                   |
| For contact details, see: http://www.openads.org/                         |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id:$
*/

/**
 * @package    OpenadsDal
 * @subpackage Statistics
 * @author     Ivan Klishch <iklishch@lohika.com>
 *
 * A file to description Dal Statistics Banner class.
 *
 */

// Required classes
require_once MAX_PATH . '/lib/OA/Dal/Statistics.php';

/**
 * The Data Abstraction Layer (DAL) class for statistics for Banner.
 *
 */
class OA_Dal_Statistics_Banner extends OA_Dal_Statistics
{
   /**
    * This method returns statistics for a given banner, broken down by day.
    *
    * @param integer $bannerId The ID of the banner to view statistics
    * @param date $oStartDate The date from which to get statistics (inclusive)
    * @param date $oEndDate The date to which to get statistics (inclusive)
    *
    * @return RecordSet
    *   <ul>
    *   <li><b>day date</b> The day
    *   <li><b>requests integer</b> The number of requests for the day
    *   <li><b>impressions integer</b> The number of impressions for the day
    *   <li><b>clicks integer</b> The number of clicks for the day
    *   <li><b>revenue decimal</b> The revenue earned for the day
    *   </ul>
    *
    */
    function getBannerDailyStatistics($bannerId, $oStartDate, $oEndDate)
    {
        $bannerId  = $this->oDbh->quote($bannerId, 'integer');

        $aConf = $GLOBALS['_MAX']['CONF'];

		$query = "
            SELECT
                SUM(s.impressions) AS impressions,
                SUM(s.clicks) AS clicks,
                SUM(s.requests) AS requests,
                SUM(s.total_revenue) AS revenue,
                s.day AS day
            FROM
                {$aConf['table']['prefix']}{$aConf['table']['banners']} AS b,
                {$aConf['table']['prefix']}{$aConf['table']['data_summary_ad_hourly']} AS s
            WHERE
                b.bannerid = $bannerId
                AND
                b.bannerid = s.ad_id
                " . $this->getWhereDate($oStartDate, $oEndDate) . "
            GROUP BY
                s.day
        ";

        return DBC::NewRecordSet($query);
    }

   /**
    * This method returns statistics for a given banner, broken down by publisher.
    *
    * @param integer $bannerId The ID of the banner to view statistics
    * @param date $oStartDate The date from which to get statistics (inclusive)
    * @param date $oEndDate The date to which to get statistics (inclusive)
    *
    * @return RecordSet
    *   <ul>
    *   <li><b>publisherID integer</b> The ID of the publisher
    *   <li><b>publisherName string (255)</b> The name of the publisher
    *   <li><b>requests integer</b> The number of requests for the day
    *   <li><b>impressions integer</b> The number of impressions for the day
    *   <li><b>clicks integer</b> The number of clicks for the day
    *   <li><b>revenue decimal</b> The revenue earned for the day
    *   </ul>
    *
    */
    function getBannerPublisherStatistics($bannerId, $oStartDate, $oEndDate)
    {
        $bannerId  = $this->oDbh->quote($bannerId, 'integer');

        $aConf = $GLOBALS['_MAX']['CONF'];

		$query = "
            SELECT
                SUM(s.impressions) AS impressions,
                SUM(s.clicks) AS clicks,
                SUM(s.requests) AS requests,
                SUM(s.total_revenue) AS revenue,
                p.affiliateid AS publisherID,
                p.name AS publisherName
            FROM
                {$aConf['table']['prefix']}{$aConf['table']['banners']} AS b,

                {$aConf['table']['prefix']}{$aConf['table']['zones']} AS z,
                {$aConf['table']['prefix']}{$aConf['table']['affiliates']} AS p,

                {$aConf['table']['prefix']}{$aConf['table']['data_summary_ad_hourly']} AS s
            WHERE
                b.bannerid = $bannerId
                AND
                b.bannerid = s.ad_id

                AND
                p.affiliateid = z.affiliateid
                AND
                z.zoneid = s.zone_id
                " . $this->getWhereDate($oStartDate, $oEndDate) . "
            GROUP BY
                publisherID
        ";

        return DBC::NewRecordSet($query);
    }

   /**
    * This method returns statistics for a given banner, broken down by zone.
    *
    * @param integer $bannerId The ID of the banner to view statistics
    * @param date $oStartDate The date from which to get statistics (inclusive)
    * @param date $oEndDate The date to which to get statistics (inclusive)
    *
    * @return RecordSet
    *   <ul>
    *   <li><b>publisherID integer</b> The ID of the publisher
    *   <li><b>publisherName string (255)</b> The name of the publisher
    *   <li><b>zoneID integer</b> The ID of the zone
    *   <li><b>zoneName string (255)</b> The name of the zone
    *   <li><b>requests integer</b> The number of requests for the day
    *   <li><b>impressions integer</b> The number of impressions for the day
    *   <li><b>clicks integer</b> The number of clicks for the day
    *   <li><b>revenue decimal</b> The revenue earned for the day
    *   </ul>
    *
    */
    function getBannerZoneStatistics($bannerId, $oStartDate, $oEndDate)
    {
        $bannerId  = $this->oDbh->quote($bannerId, 'integer');

        $aConf = $GLOBALS['_MAX']['CONF'];

		$query = "
            SELECT
                SUM(s.impressions) AS impressions,
                SUM(s.clicks) AS clicks,
                SUM(s.requests) AS requests,
                SUM(s.total_revenue) AS revenue,
                p.affiliateid AS publisherID,
                p.name AS publisherName,
                z.zoneid AS zoneID,
                z.zonename AS zoneName
            FROM
                {$aConf['table']['prefix']}{$aConf['table']['banners']} AS b,

                {$aConf['table']['prefix']}{$aConf['table']['zones']} AS z,
                {$aConf['table']['prefix']}{$aConf['table']['affiliates']} AS p,

                {$aConf['table']['prefix']}{$aConf['table']['data_summary_ad_hourly']} AS s
            WHERE
                b.bannerid = $bannerId
                AND
                b.bannerid = s.ad_id

                AND
                p.affiliateid = z.affiliateid
                AND
                z.zoneid = s.zone_id
                " . $this->getWhereDate($oStartDate, $oEndDate) . "
            GROUP BY
                zoneID
        ";

        return DBC::NewRecordSet($query);
    }

}

?>