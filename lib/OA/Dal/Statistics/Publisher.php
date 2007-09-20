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
 * @author     Andriy Petlyovanyy <apetlyovanyy@lohika.com>
 *
 * A file to description Dal Statistics Publisher class.
 *
 */

// Required classes
require_once MAX_PATH . '/lib/OA/Dal/Statistics.php';

/**
 * The Data Abstraction Layer (DAL) class for statistics for Publisher.
 *
 */
class OA_Dal_Statistics_Publisher extends OA_Dal_Statistics
{
   /**
    * This method returns statistics for a given publisher, broken down by day.
    *
    * @param integer $publisherId The ID of the agency to view statistics
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
    function getPublisherDailyStatistics($publisherId, $oStartDate, $oEndDate)
    {
        $publisherId = $this->oDbh->quote($publisherId, 'integer');
        $aConf = $GLOBALS['_MAX']['CONF'];

		$query = "
            SELECT
                SUM(s.impressions) AS impressions,
                SUM(s.clicks) AS clicks,
                SUM(s.requests) AS requests,
                SUM(s.total_revenue) AS revenue,
                s.day AS day
            FROM
                {$aConf['table']['prefix']}{$aConf['table']['zones']} AS z,
                {$aConf['table']['prefix']}{$aConf['table']['affiliates']} AS p,
                {$aConf['table']['prefix']}{$aConf['table']['data_summary_ad_hourly']} AS s
            WHERE
                p.affiliateid = $publisherId

                AND
                p.affiliateid = z.affiliateid
                AND
                z.zoneid = s.zone_id

                " . $this->getWhereDate($oStartDate, $oEndDate) . "
            GROUP BY
                s.day
        ";

        return DBC::NewRecordSet($query);
    }

   /**
    * This method returns statistics for a given publisher, broken down by zone.
    *
    * @param integer $publisherId The ID of the publisher to view statistics
    * @param date $oStartDate The date from which to get statistics (inclusive)
    * @param date $oEndDate The date to which to get statistics (inclusive)
    *
    * @return RecordSet
    *   <ul>
    *   <li><b>zoneID integer</b> The ID of the zone
    *   <li><b>zoneName string (255)</b> The name of the zone
    *   <li><b>requests integer</b> The number of requests for the zone
    *   <li><b>impressions integer</b> The number of impressions for the zone
    *   <li><b>clicks integer</b> The number of clicks for the zone
    *   <li><b>revenue decimal</b> The revenue earned for the zone
    *   </ul>
    *
    */
    function getPublisherZoneStatistics($publisherId, $oStartDate, $oEndDate)
    {
        $publisherId = $this->oDbh->quote($publisherId, 'integer');
        $aConf = $GLOBALS['_MAX']['CONF'];

		$query = "
            SELECT
                SUM(s.impressions) AS impressions,
                SUM(s.clicks) AS clicks,
                SUM(s.requests) AS requests,
                SUM(s.total_revenue) AS revenue,
                z.zoneid AS zoneID,
                z.zonename AS zoneName
            FROM
                {$aConf['table']['prefix']}{$aConf['table']['zones']} AS z,
                {$aConf['table']['prefix']}{$aConf['table']['affiliates']} AS p,
                {$aConf['table']['prefix']}{$aConf['table']['data_summary_ad_hourly']} AS s
            WHERE
                p.affiliateid = $publisherId

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


   /**
    * This method returns statistics for a given publisher, broken down by advertiser.
    *
    * @param integer $publisherId The ID of the publisher to view statistics
    * @param date $oStartDate The date from which to get statistics (inclusive)
    * @param date $oEndDate The date to which to get statistics (inclusive)
    *
    * @return RecordSet
    *   <ul>
    *   <li><b>advertiser ID integer</b> The ID of the advertiser
    *   <li><b>advertiserName string (255)</b> The name of the advertiser
    *   <li><b>requests integer</b> The number of requests for the advertiser
    *   <li><b>impressions integer</b> The number of impressions for the advertiser
    *   <li><b>clicks integer</b> The number of clicks for the advertiser
    *   <li><b>revenue decimal</b> The revenue earned for the advertiser
    *   </ul>
    *
    */
    function getPublisherAdvertiserStatistics($publisherId, $oStartDate, $oEndDate)
    {
        $publisherId = $this->oDbh->quote($publisherId, 'integer');
        $aConf = $GLOBALS['_MAX']['CONF'];

		$query = "
            SELECT
                SUM(s.impressions) AS impressions,
                SUM(s.clicks) AS clicks,
                SUM(s.requests) AS requests,
                SUM(s.total_revenue) AS revenue,
                c.clientid AS advertiserID,
                c.clientname AS advertiserName
            FROM
                {$aConf['table']['prefix']}{$aConf['table']['clients']} AS c,
                {$aConf['table']['prefix']}{$aConf['table']['campaigns']} AS m,
                {$aConf['table']['prefix']}{$aConf['table']['banners']} AS b,

                {$aConf['table']['prefix']}{$aConf['table']['zones']} AS z,
                {$aConf['table']['prefix']}{$aConf['table']['affiliates']} AS p,
                {$aConf['table']['prefix']}{$aConf['table']['data_summary_ad_hourly']} AS s
            WHERE
                p.affiliateid = $publisherId

                AND
                c.clientid = m.clientid
                AND
                m.campaignid = b.campaignid
                AND
                b.bannerid = s.ad_id

                AND
                p.affiliateid = z.affiliateid
                AND
                z.zoneid = s.zone_id

                " . $this->getWhereDate($oStartDate, $oEndDate) . "
            GROUP BY
                advertiserID
        ";

        return DBC::NewRecordSet($query);
    }

   /**
    * This method returns statistics for a given publisher, broken down by campaign.
    *
    * @param integer $publisherId The ID of the publisher to view statistics
    * @param date $oStartDate The date from which to get statistics (inclusive)
    * @param date $oEndDate The date to which to get statistics (inclusive)
    *
    * @return RecordSet
    *   <ul>
    *   <li><b>campaignID integer</b> The ID of the campaign
    *   <li><b>campaignName string (255)</b> The name of the campaign
    *   <li><b>advertiserID integer</b> The ID advertiser
    *   <li><b>advertiserName string</b> The name advertiser
    *   <li><b>requests integer</b> The number of requests for the campaign
    *   <li><b>impressions integer</b> The number of impressions for the campaign
    *   <li><b>clicks integer</b> The number of clicks for the campaign
    *   <li><b>revenue decimal</b> The revenue earned for the campaign
    *   </ul>
    *
    */
    function getPublisherCampaignStatistics($publisherId, $oStartDate, $oEndDate)
    {
        $publisherId = $this->oDbh->quote($publisherId, 'integer');
        $aConf = $GLOBALS['_MAX']['CONF'];

		$query = "
            SELECT
                SUM(s.impressions) AS impressions,
                SUM(s.clicks) AS clicks,
                SUM(s.requests) AS requests,
                SUM(s.total_revenue) AS revenue,
                m.campaignid AS campaignID,
                m.campaignname AS campaignName,
                c.clientid AS advertiserID,
                c.clientname AS advertiserName
            FROM
                {$aConf['table']['prefix']}{$aConf['table']['clients']} AS c,
                {$aConf['table']['prefix']}{$aConf['table']['campaigns']} AS m,
                {$aConf['table']['prefix']}{$aConf['table']['banners']} AS b,

                {$aConf['table']['prefix']}{$aConf['table']['zones']} AS z,
                {$aConf['table']['prefix']}{$aConf['table']['affiliates']} AS p,
                {$aConf['table']['prefix']}{$aConf['table']['data_summary_ad_hourly']} AS s
            WHERE
                p.affiliateid = $publisherId

                AND
                c.clientid = m.clientid
                AND
                m.campaignid = b.campaignid
                AND
                b.bannerid = s.ad_id

                AND
                p.affiliateid = z.affiliateid
                AND
                z.zoneid = s.zone_id

                " . $this->getWhereDate($oStartDate, $oEndDate) . "
            GROUP BY
                campaignID
        ";

        return DBC::NewRecordSet($query);
    }

   /**
    * This method returns statistics for a given publisher, broken down by banner.
    *
    * @param integer $publisherId The ID of the publisher to view statistics
    * @param date $oStartDate The date from which to get statistics (inclusive)
    * @param date $oEndDate The date to which to get statistics (inclusive)
    *
    * @return RecordSet
    *   <ul>
    *   <li><b>bannerID integer</b> The ID of the banner
    *   <li><b>bannerName string (255)</b> The name of the banner
    *   <li><b>campaignID integer</b> The ID of the banner
    *   <li><b>campaignName string (255)</b> The name of the banner
    *   <li><b>advertiserID integer</b> The ID advertiser
    *   <li><b>advertiserName string</b> The name advertiser
    *   <li><b>requests integer</b> The number of requests for the banner
    *   <li><b>impressions integer</b> The number of impressions for the banner
    *   <li><b>clicks integer</b> The number of clicks for the banner
    *   <li><b>revenue decimal</b> The revenue earned for the banner
    *   </ul>
    *
    */
    function getPublisherBannerStatistics($publisherId, $oStartDate, $oEndDate)
    {
        $publisherId = $this->oDbh->quote($publisherId, 'integer');
        $aConf = $GLOBALS['_MAX']['CONF'];

		$query = "
            SELECT
                SUM(s.impressions) AS impressions,
                SUM(s.clicks) AS clicks,
                SUM(s.requests) AS requests,
                SUM(s.total_revenue) AS revenue,
                m.campaignid AS campaignID,
                m.campaignname AS campaignName,
                c.clientid AS advertiserID,
                c.clientname AS advertiserName,
                b.bannerid AS bannerID,
                b.description AS bannerName
            FROM
                {$aConf['table']['prefix']}{$aConf['table']['clients']} AS c,
                {$aConf['table']['prefix']}{$aConf['table']['campaigns']} AS m,
                {$aConf['table']['prefix']}{$aConf['table']['banners']} AS b,

                {$aConf['table']['prefix']}{$aConf['table']['zones']} AS z,
                {$aConf['table']['prefix']}{$aConf['table']['affiliates']} AS p,
                {$aConf['table']['prefix']}{$aConf['table']['data_summary_ad_hourly']} AS s
            WHERE
                p.affiliateid = $publisherId

                AND
                c.clientid = m.clientid
                AND
                m.campaignid = b.campaignid
                AND
                b.bannerid = s.ad_id

                AND
                p.affiliateid = z.affiliateid
                AND
                z.zoneid = s.zone_id

                " . $this->getWhereDate($oStartDate, $oEndDate) . "
            GROUP BY
                bannerID
        ";

        return DBC::NewRecordSet($query);
    }


}

?>