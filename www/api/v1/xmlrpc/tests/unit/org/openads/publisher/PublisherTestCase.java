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

package org.openads.publisher;

import java.net.MalformedURLException;
import java.net.URL;
import java.util.HashMap;
import java.util.Map;

import org.apache.xmlrpc.XmlRpcException;
import org.apache.xmlrpc.client.XmlRpcClientConfigImpl;
import org.openads.agency.AgencyTestCase;
import org.openads.config.GlobalSettings;
import org.openads.utils.TextUtils;

/**
 * Base class for all publiser web service tests
 * 
 * @author <a href="mailto:apetlyovanyy@lohika.com">Andriy Petlyovanyy</a>
 */
public class PublisherTestCase extends AgencyTestCase {

	protected static final String ADD_PUBLISHER_METHOD = "addPublisher";
	protected static final String DELETE_PUBLISHER_METHOD = "deletePublisher";
	protected static final String MODIFY_PUBLISHER_METHOD = "modifyPublisher";
	protected final static String PUBLISHER_ZONE_STATISTICS_METHOD = "publisherZoneStatistics";
	protected final static String PUBLISHER_CAMPAIGN_STATISTICS_METHOD = "publisherCampaignStatistics";
	protected static final String PUBLISHER_DAILY_STATISTICS_METHOD = "publisherDailyStatistics";
	protected final static String PUBLISHER_BANNER_STATISTICS_METHOD = "publisherBannerStatistics";
	protected final static String PUBLISHER_ADVERTISER_STATISTICS_METHOD = "publisherAdvertiserStatistics";

	protected static final String PUBLISHER_ID = "publisherId";
	protected static final String USERNAME = "username";
	protected static final String PASSWORD = "password";
	protected static final String PUBLISHER_NAME = "publisherName";
	protected static final String EMAIL_ADDRESS = "emailAddress";

	protected Integer agencyId = null;

	protected void setUp() throws Exception {
		super.setUp();

		agencyId = createAgency();

		((XmlRpcClientConfigImpl) client.getClientConfig())
				.setServerURL(new URL(GlobalSettings.getPublisherServiceUrl()));
	}

	protected void tearDown() throws Exception {

		deleteAgency(agencyId);

		super.tearDown();
	}

	/**
	 * @return Publisher id
	 * @throws XmlRpcException
	 * @throws MalformedURLException
	 */
	public Integer createPublisher() throws XmlRpcException,
			MalformedURLException {
		((XmlRpcClientConfigImpl) client.getClientConfig())
				.setServerURL(new URL(GlobalSettings.getPublisherServiceUrl()));
		Map<String, Object> struct = new HashMap<String, Object>();
		struct.put("agencyId", agencyId);
		struct.put("publisherName", "test Publisher");
		struct.put("contactName", "Oleh 2");
		struct.put("emailAddress", "test@url.com");
		struct.put("username", TextUtils.generateUniqueName("Publisher"));
		struct.put("password", "paskjrfgkl");
		Object[] params = new Object[] { sessionId, struct };
		final Integer result = (Integer) client.execute(ADD_PUBLISHER_METHOD,
				params);
		return result;
	}

	/**
	 * @param id -
	 *            id of publisher you want to remove
	 * @throws XmlRpcException
	 * @throws MalformedURLException
	 */
	public boolean deletePublisher(Integer id) throws XmlRpcException,
			MalformedURLException {
		((XmlRpcClientConfigImpl) client.getClientConfig())
				.setServerURL(new URL(GlobalSettings.getPublisherServiceUrl()));
		return (Boolean) client.execute(DELETE_PUBLISHER_METHOD, new Object[] {
				sessionId, id });
	}

	public Object execute(String method, Object[] params)
			throws XmlRpcException, MalformedURLException {
		// set URL
		((XmlRpcClientConfigImpl) client.getClientConfig())
				.setServerURL(new URL(GlobalSettings.getPublisherServiceUrl()));

		return client.execute(method, params);
	}
}
