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

package org.openads.banner;

import java.net.MalformedURLException;

import org.apache.xmlrpc.XmlRpcException;
import org.openads.utils.ErrorMessage;
import org.openads.utils.TextUtils;

/**
 * Verify Delete Banner method
 * 
 * @author <a href="mailto:apetlyovanyy@lohika.com">Andriy Petlyovanyy</a>
 */
public class TestDeleteBanner extends BannerTestCase {
	/**
	 * Execute test method with error
	 * 
	 * @param params -
	 *            parameters for test method
	 * @param errorMsg -
	 *            true error messages
	 * @throws MalformedURLException
	 */
	private void executeDeleteBannerWithError(Object[] params, String errorMsg)
			throws MalformedURLException {
		try {
			execute(DELETE_BANNER_METHOD, params);
			fail(DELETE_BANNER_METHOD
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
	 * @throws MalformedURLException
	 */
	public void testDeleteBannerAllReqAndSomeOptionalFields()
			throws XmlRpcException, MalformedURLException {

		int bannerId = createBanner();
		assertNotNull("Can't add banner.", bannerId);
		final Boolean result = (Boolean) execute(DELETE_BANNER_METHOD,
				new Object[] { sessionId, bannerId });
		assertTrue("Can't delete banner.", result);

	}

	/**
	 * Test method without some required fields.
	 * 
	 * @throws MalformedURLException
	 */
	public void testDeleteBannerWithoutSomeRequiredFields()
			throws MalformedURLException {
		Object[] params = new Object[] { sessionId, null };

		executeDeleteBannerWithError(params,
				ErrorMessage.NULL_VALUES_ARE_NOT_SUPPORTED);
	}

	/**
	 * Try to delete banner with unknown id
	 * 
	 * @throws XmlRpcException
	 * @throws MalformedURLException
	 */
	public void testDeleteBannerUnknownIdError() throws XmlRpcException,
			MalformedURLException {
		final Integer id = createBanner();
		deleteBanner(id);
		Object[] params = new Object[] { sessionId, id };

		executeDeleteBannerWithError(params, ErrorMessage
				.getMessage(ErrorMessage.getMessage(
						ErrorMessage.UNKNOWN_ID_ERROR, BANNER_ID)));
	}

	/**
	 * Test method with fields that has value of wrong type (error).
	 * 
	 * @throws MalformedURLException
	 */
	public void testDeleteBannerWrongTypeError() throws MalformedURLException {
		Object[] params = new Object[] { sessionId, TextUtils.NOT_INTEGER };

		executeDeleteBannerWithError(params, ErrorMessage.getMessage(
				ErrorMessage.INCORRECT_PARAMETERS_WANTED_INT_GOT_STRING, "2"));
	}
}
