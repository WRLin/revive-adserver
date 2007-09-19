<?php

/*
+---------------------------------------------------------------------------+
| Openads v${RELEASE_MAJOR_MINOR}                                                              |
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
$Id$
*/

require_once MAX_PATH . '/lib/max/Entity/Ad.php';
require_once MAX_PATH . '/lib/max/Maintenance/Priority/Entities.php';
require_once MAX_PATH . '/lib/max/Dal/tests/util/DalUnitTestCase.php';

require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Dal/Maintenance/Priority.php';
require_once MAX_PATH . '/lib/OA/DB/Table/Priority.php';
require_once MAX_PATH . '/lib/OA/ServiceLocator.php';
require_once MAX_PATH . '/lib/pear/Date.php';
require_once 'DB/QueryTool.php';

// pgsql execution time before refactor: 82.089s
// pgsql execution time after refactor: 22.478s

/**
 * A class for testing the non-DB specific OA_Dal_Maintenance_Priority class.
 *
 * @package    OpenadsDal
 * @subpackage TestSuite
 * @author     Monique Szpak <monique.szpak@openads.org>
 * @author     James Floyd <james@m3.net>
 * @author     Andrew Hill <andrew.hill@openads.org>
 * @author     Demian Turner <demian@m3.net>
 */
class Test_OA_Dal_Maintenance_Priority_AdZoneAssociationsByAds extends UnitTestCase
{
    /**
     * The constructor method.
     */
    function Test_OA_Dal_Maintenance_Priority_AdZoneAssociationsByAds()
    {
        $this->UnitTestCase();
    }

    /**
     * A method to test the getAdZoneAssociationsByAds method.
     *
     * Test 1: Test with bad input, and ensure that an empty array is retuend.
     * Test 2: Test with no data, and ensure that an empty array is returned.
     * Test 3: Test with one ad/zone link, and ensure the correct data is returned.
     * Test 4: Test with a more complex set of data.
     */
    function testGetAdZoneAssociationsByAds()
    {
        $conf = $GLOBALS['_MAX']['CONF'];
        $oDbh =& OA_DB::singleton();
        $oDal = new OA_Dal_Maintenance_Priority();

        // Test 1
        $result = $oDal->getAdZoneAssociationsByAds(1);
        $this->assertTrue(is_array($result));
        $this->assertEqual(count($result), 0);

        // Test 2
        $aAdIds = array(1);
        $result = $oDal->getAdZoneAssociationsByAds($aAdIds);
        $this->assertTrue(is_array($result));
        $this->assertEqual(count($result), 0);

        // Test 3

        $doAdZone = OA_Dal::factoryDO('ad_zone_assoc');

        $doAdZone->ad_id = 1;
        $doAdZone->zone_id = 1;
        $doAdZone->link_type = 1;
        $idAdZone = DataGenerator::generateOne($doAdZone);

        $aAdIds = array(1);
        $result = $oDal->getAdZoneAssociationsByAds($aAdIds);
        $this->assertTrue(is_array($result));
        $this->assertEqual(count($result), 1);
        $this->assertTrue(is_array($result[1]));
        $this->assertEqual(count($result[1]), 1);
        $this->assertTrue(is_array($result[1][0]));
        $this->assertEqual(count($result[1][0]), 1);
        $this->assertTrue(isset($result[1][0]['zone_id']));
        $this->assertEqual($result[1][0]['zone_id'], 1);
        DataGenerator::cleanUp();

        // Test 4
        $doAdZone->ad_id = 1;
        $doAdZone->zone_id = 1;
        $doAdZone->link_type = 1;
        $idAdZone = DataGenerator::generateOne($doAdZone);
        $doAdZone->ad_id = 1;
        $doAdZone->zone_id = 2;
        $doAdZone->link_type = 1;
        $idAdZone = DataGenerator::generateOne($doAdZone);
        $doAdZone->ad_id = 1;
        $doAdZone->zone_id = 7;
        $doAdZone->link_type = 1;
        $idAdZone = DataGenerator::generateOne($doAdZone);
        $doAdZone->ad_id = 2;
        $doAdZone->zone_id = 2;
        $doAdZone->link_type = 1;
        $idAdZone = DataGenerator::generateOne($doAdZone);
        $doAdZone->ad_id = 2;
        $doAdZone->zone_id = 7;
        $doAdZone->link_type = 0;
        $idAdZone = DataGenerator::generateOne($doAdZone);
        $doAdZone->ad_id = 3;
        $doAdZone->zone_id = 1;
        $doAdZone->link_type = 1;
        $idAdZone = DataGenerator::generateOne($doAdZone);
        $doAdZone->ad_id = 3;
        $doAdZone->zone_id = 9;
        $doAdZone->link_type = 1;
        $idAdZone = DataGenerator::generateOne($doAdZone);

        $aAdIds = array(1, 2, 3);
        $result = $oDal->getAdZoneAssociationsByAds($aAdIds);
        $this->assertTrue(is_array($result));
        $this->assertEqual(count($result), 3);
        $this->assertTrue(is_array($result[1]));
        $this->assertEqual(count($result[1]), 3);
        $this->assertTrue(is_array($result[1][0]));
        $this->assertEqual(count($result[1][0]), 1);
        $this->assertTrue(isset($result[1][0]['zone_id']));
        $this->assertEqual($result[1][0]['zone_id'], 1);
        $this->assertTrue(is_array($result[1][1]));
        $this->assertEqual(count($result[1][1]), 1);
        $this->assertTrue(isset($result[1][1]['zone_id']));
        $this->assertEqual($result[1][1]['zone_id'], 2);
        $this->assertTrue(is_array($result[1][2]));
        $this->assertEqual(count($result[1][2]), 1);
        $this->assertTrue(isset($result[1][2]['zone_id']));
        $this->assertEqual($result[1][2]['zone_id'], 7);
        $this->assertTrue(is_array($result[2]));
        $this->assertEqual(count($result[2]), 1);
        $this->assertTrue(is_array($result[2][0]));
        $this->assertEqual(count($result[2][0]), 1);
        $this->assertTrue(isset($result[2][0]['zone_id']));
        $this->assertEqual($result[2][0]['zone_id'], 2);
        $this->assertTrue(is_array($result[3]));
        $this->assertEqual(count($result[3]), 2);
        $this->assertTrue(is_array($result[3][0]));
        $this->assertEqual(count($result[3][0]), 1);
        $this->assertTrue(isset($result[3][0]['zone_id']));
        $this->assertEqual($result[3][0]['zone_id'], 1);
        $this->assertTrue(is_array($result[3][1]));
        $this->assertEqual(count($result[3][1]), 1);
        $this->assertTrue(isset($result[3][1]['zone_id']));
        $this->assertEqual($result[3][1]['zone_id'], 9);
        DataGenerator::cleanUp();
    }


    /**
     * A method to test the getAdZoneAssociationsByAds method.
     *
     * Test 1: Test with bad input, and ensure that an empty array is retuend.
     * Test 2: Test with no data, and ensure that an empty array is returned.
     * Test 3: Test with one ad/zone link, and ensure the correct data is returned.
     * Test 4: Test with a more complex set of data.
     */
    function OLD_testGetAdZoneAssociationsByAds()
    {
        $conf = $GLOBALS['_MAX']['CONF'];
        $oDbh =& OA_DB::singleton();
        $oDal = new OA_Dal_Maintenance_Priority();

        // Test 1
        $result = $oDal->getAdZoneAssociationsByAds(1);
        $this->assertTrue(is_array($result));
        $this->assertEqual(count($result), 0);

        // Test 2
        $aAdIds = array(1);
        $result = $oDal->getAdZoneAssociationsByAds($aAdIds);
        $this->assertTrue(is_array($result));
        $this->assertEqual(count($result), 0);

        // Test 3
        $query = "
            INSERT INTO
                ".$oDbh->quoteIdentifier($conf['table']['prefix'].$conf['table']['ad_zone_assoc'],true)."
                (
                    ad_id,
                    zone_id,
                    link_type
                )
            VALUES
                (?, ?, ?)";
        $aTypes = array(
            'integer',
            'integer',
            'integer'
        );
        $st = $oDbh->prepare($query, $aTypes, MDB2_PREPARE_MANIP);
        $aData = array(
            1,
            1,
            1
        );
        $rows = $st->execute($aData);
        $aAdIds = array(1);
        $result = $oDal->getAdZoneAssociationsByAds($aAdIds);
        $this->assertTrue(is_array($result));
        $this->assertEqual(count($result), 1);
        $this->assertTrue(is_array($result[1]));
        $this->assertEqual(count($result[1]), 1);
        $this->assertTrue(is_array($result[1][0]));
        $this->assertEqual(count($result[1][0]), 1);
        $this->assertTrue(isset($result[1][0]['zone_id']));
        $this->assertEqual($result[1][0]['zone_id'], 1);
        TestEnv::restoreEnv('dropTmpTables');

        // Test 4
        $aData = array(
            1,
            1,
            1
        );
        $rows = $st->execute($aData);
        $aData = array(
            1,
            2,
            1
        );
        $rows = $st->execute($aData);
        $aData = array(
            1,
            7,
            1
        );
        $rows = $st->execute($aData);
        $aData = array(
            2,
            2,
            1
        );
        $rows = $st->execute($aData);
        $aData = array(
            2,
            7,
            0
        );
        $rows = $st->execute($aData);
        $aData = array(
            3,
            1,
            1
        );
        $rows = $st->execute($aData);
        $aData = array(
            3,
            9,
            1
        );
        $rows = $st->execute($aData);
        $aAdIds = array(1, 2, 3);
        $result = $oDal->getAdZoneAssociationsByAds($aAdIds);
        $this->assertTrue(is_array($result));
        $this->assertEqual(count($result), 3);
        $this->assertTrue(is_array($result[1]));
        $this->assertEqual(count($result[1]), 3);
        $this->assertTrue(is_array($result[1][0]));
        $this->assertEqual(count($result[1][0]), 1);
        $this->assertTrue(isset($result[1][0]['zone_id']));
        $this->assertEqual($result[1][0]['zone_id'], 1);
        $this->assertTrue(is_array($result[1][1]));
        $this->assertEqual(count($result[1][1]), 1);
        $this->assertTrue(isset($result[1][1]['zone_id']));
        $this->assertEqual($result[1][1]['zone_id'], 2);
        $this->assertTrue(is_array($result[1][2]));
        $this->assertEqual(count($result[1][2]), 1);
        $this->assertTrue(isset($result[1][2]['zone_id']));
        $this->assertEqual($result[1][2]['zone_id'], 7);
        $this->assertTrue(is_array($result[2]));
        $this->assertEqual(count($result[2]), 1);
        $this->assertTrue(is_array($result[2][0]));
        $this->assertEqual(count($result[2][0]), 1);
        $this->assertTrue(isset($result[2][0]['zone_id']));
        $this->assertEqual($result[2][0]['zone_id'], 2);
        $this->assertTrue(is_array($result[3]));
        $this->assertEqual(count($result[3]), 2);
        $this->assertTrue(is_array($result[3][0]));
        $this->assertEqual(count($result[3][0]), 1);
        $this->assertTrue(isset($result[3][0]['zone_id']));
        $this->assertEqual($result[3][0]['zone_id'], 1);
        $this->assertTrue(is_array($result[3][1]));
        $this->assertEqual(count($result[3][1]), 1);
        $this->assertTrue(isset($result[3][1]['zone_id']));
        $this->assertEqual($result[3][1]['zone_id'], 9);
        TestEnv::restoreEnv('dropTmpTables');
    }
}
?>