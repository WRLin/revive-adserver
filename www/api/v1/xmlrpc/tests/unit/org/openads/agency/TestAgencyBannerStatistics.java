/*
+---------------------------------------------------------------------------+
| Openads v2.5                                                              |
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
|  Copyright 2003-2007 Openads Limited                                      |
|                                                                           |
|  Licensed under the Apache License, Version 2.0 (the "License");          |
|  you may not use this file except in compliance with the License.         |
|  You may obtain a copy of the License at                                  |
|                                                                           |
|    http://www.apache.org/licenses/LICENSE-2.0                             |
|                                                                           |
|  Unless required by applicable law or agreed to in writing, software      |
|  distributed under the License is distributed on an "AS IS" BASIS,        |
|  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. |
|  See the License for the specific language governing permissions and      |
|  limitations under the License.                                           |
+---------------------------------------------------------------------------+
$Id:$
*/

package org.openads.agency;

import java.net.MalformedURLException;

import org.apache.xmlrpc.XmlRpcException;
import org.openads.utils.DateUtils;
import org.openads.utils.ErrorMessage;
import org.openads.utils.TextUtils;

/**
 * Verify Agency Banner Statistics method
 * 
 * @author <a href="mailto:apetlyovanyy@lohika.com">Andriy Petlyovanyy</a>
 */
public class TestAgencyBannerStatistics extends AgencyTestCase {
	private Integer agencyId;

	protected void setUp() throws Exception {
		super.setUp();
		agencyId = createAgency();
	}

	protected void tearDown() throws Exception {
		deleteAgency(agencyId);
		super.tearDown();
	}

	/**
	 * Execute test method with error
	 * 
	 * @param params -
	 *            parameters for test method
	 * @param errorMsg -
	 *            true error messages
	 * @throws MalformedURLException
	 */
	private void executeAgencyBannerStatisticsWithError(Object[] params,
			String errorMsg) throws MalformedURLException {
		try {
			execute(AGENCY_BANNER_STATISTICS_METHOD, params);
			fail(AGENCY_BANNER_STATISTICS_METHOD
					+ " executed successfully, but it shouldn't.");
		} catch (XmlRpcException e) {
			assertEquals(ErrorMessage.WRONG_ERROR_MESSAGE, errorMsg, e
					.getMessage());
		}

	}

	/**
	 * Test method with all required fields and some optional.
	 * 
	 * @throws XmlRpcException
	 */
	public void testAgencyBannerStatisticsAllReqAndSomeOptionalFields()
			throws XmlRpcException {
		Object[] params = new Object[] { sessionId, agencyId,
				DateUtils.MIN_DATE_VALUE };
		Object[] result = (Object[]) client.execute(
				AGENCY_BANNER_STATISTICS_METHOD, params);

		assertNotNull(result);
	}

	/**
	 * Test method without some required fields.
	 * 
	 * @throws Exception
	 */
	public void testAgencyBannerStatisticsWithoutSomeRequiredFields()
			throws Exception {
		Object[] params = new Object[] { sessionId, null,
				DateUtils.MIN_DATE_VALUE, DateUtils.MAX_DATE_VALUE };
		try {
			client.execute(AGENCY_BANNER_STATISTICS_METHOD, params);
			fail(ErrorMessage.METHOD_EXECUTED_SUCCESSFULLY_BUT_SHOULD_NOT_HAVE);
		} catch (XmlRpcException e) {
			assertEquals(ErrorMessage.WRONG_ERROR_MESSAGE,
					ErrorMessage.NULL_VALUES_ARE_NOT_SUPPORTED, e.getMessage());
		}
	}

	/**
	 * Test method with fields that has value greater than max.
	 * 
	 * @throws MalformedURLException
	 * @throws XmlRpcException
	 */
	public void testAgencyBannerStatisticsGreaterThanMaxFieldValueError()
			throws MalformedURLException, XmlRpcException {
		Object[] params = new Object[] { sessionId, agencyId,
				DateUtils.MIN_DATE_VALUE, DateUtils.DATE_GREATER_THAN_MAX };

		try {
			client.execute(AGENCY_BANNER_STATISTICS_METHOD, params);
			fail(ErrorMessage.METHOD_EXECUTED_SUCCESSFULLY_BUT_SHOULD_NOT_HAVE);
		} catch (XmlRpcException e) {
			assertEquals(ErrorMessage.YEAR_SHOULD_BE_IN_RANGE_1970_2038, e
					.getMessage());
		}
	}

	/**
	 * Test method with fields that has value less than min
	 * 
	 * @throws MalformedURLException
	 */
	public void testAgencyBannerStatisticsLessThanMinFieldValueError()
			throws MalformedURLException {
		Object[] params = new Object[] { sessionId, agencyId,
				DateUtils.DATE_LESS_THAN_MIN, DateUtils.MAX_DATE_VALUE };

		try {
			client.execute(AGENCY_BANNER_STATISTICS_METHOD, params);
			fail(ErrorMessage.METHOD_EXECUTED_SUCCESSFULLY_BUT_SHOULD_NOT_HAVE);
		} catch (XmlRpcException e) {
			assertEquals(ErrorMessage.YEAR_SHOULD_BE_IN_RANGE_1970_2038, e
					.getMessage());
		}
	}

	/**
	 * Test method with fields that has min. allowed values.
	 * 
	 * @throws XmlRpcException
	 * @throws MalformedURLException
	 */
	public void testAgencyBannerStatisticsMinValues() throws XmlRpcException,
			MalformedURLException {
		Object[] params = new Object[] { sessionId, agencyId,
				DateUtils.MIN_DATE_VALUE, DateUtils.MIN_DATE_VALUE };

		client.execute(AGENCY_BANNER_STATISTICS_METHOD, params);
	}

	/**
	 * Test method with fields that has max. allowed values.
	 * 
	 * @throws XmlRpcException
	 * @throws MalformedURLException
	 */
	public void testAgencyBannerStatisticsMaxValues() throws XmlRpcException,
			MalformedURLException {
		Object[] params = new Object[] { sessionId, agencyId,
				DateUtils.MAX_DATE_VALUE, DateUtils.MAX_DATE_VALUE };

		client.execute(AGENCY_BANNER_STATISTICS_METHOD, params);
	}

	/**
	 * AgencyBannerStatistics with unknown id
	 * 
	 * @throws XmlRpcException
	 * @throws MalformedURLException
	 */
	public void testAgencyBannerStatisticsUnknownIdError()
			throws XmlRpcException, MalformedURLException {
		final Integer id = createAgency();
		deleteAgency(id);
		Object[] params = new Object[] { sessionId, id };

		try {
			client.execute(AGENCY_BANNER_STATISTICS_METHOD, params);
			fail(ErrorMessage.METHOD_EXECUTED_SUCCESSFULLY_BUT_SHOULD_NOT_HAVE);
		} catch (XmlRpcException e) {
			assertEquals(ErrorMessage.WRONG_ERROR_MESSAGE, ErrorMessage
					.getMessage(ErrorMessage.UNKNOWN_ID_ERROR, "agencyId"), e
					.getMessage());
		}
	}

	/**
	 * AgencyBannerStatistics when end date is before start date
	 * 
	 * @throws XmlRpcException
	 * @throws MalformedURLException
	 */
	public void testAgencyBannerStatisticsDateError() throws XmlRpcException,
			MalformedURLException {
		Object[] params = new Object[] { sessionId, agencyId,
				DateUtils.MAX_DATE_VALUE, DateUtils.MIN_DATE_VALUE };

		try {
			client.execute(AGENCY_BANNER_STATISTICS_METHOD, params);
			fail(ErrorMessage.METHOD_EXECUTED_SUCCESSFULLY_BUT_SHOULD_NOT_HAVE);
		} catch (XmlRpcException e) {
			assertEquals(ErrorMessage.WRONG_ERROR_MESSAGE,
					ErrorMessage.START_DATE_IS_AFTER_END_DATE, e.getMessage());
		}
	}

	/**
	 * Test method with fields that has value of wrong type (error).
	 * 
	 * @throws MalformedURLException
	 */
	public void testAgencyBannerStatisticsWrongTypeError()
			throws MalformedURLException {
		Object[] params = new Object[] { sessionId, agencyId,
				TextUtils.NOT_DATE, DateUtils.MAX_DATE_VALUE };
		executeAgencyBannerStatisticsWithError(params, ErrorMessage.getMessage(
				ErrorMessage.INCORRECT_PARAMETERS_WANTED_DATE_GOT_STRING, "3"));

		params = new Object[] { sessionId, agencyId, DateUtils.MIN_DATE_VALUE,
				TextUtils.NOT_DATE };
		executeAgencyBannerStatisticsWithError(params, ErrorMessage.getMessage(
				ErrorMessage.INCORRECT_PARAMETERS_WANTED_DATE_GOT_STRING, "4"));
	}
}
