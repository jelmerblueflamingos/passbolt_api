<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SARL (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Passbolt SARL (https://www.passbolt.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.passbolt.com Passbolt(tm)
 * @since         2.0.0
 */

namespace App\Test\TestCase\Controller\Users;

use App\Utility\Common;
use App\Test\Lib\AppIntegrationTestCase;

class UsersViewControllerTest extends AppIntegrationTestCase
{
    public $fixtures = ['app.users', 'app.profiles', 'app.gpgkeys', 'app.roles'];

    public function testUsersViewGetSuccess()
    {
        $this->authenticateAs('ada');
        $uuid = Common::uuid('user.id.ada');
        $this->getJson('/users/' . $uuid . '.json?api-version=2');
        $this->assertSuccess();
        $this->assertNotNull($this->_responseJsonBody);

        $this->assertUserAttributes($this->_responseJsonBody);
        $this->assertObjectHasAttribute('profile', $this->_responseJsonBody);
        $this->assertProfileAttributes($this->_responseJsonBody->profile);
        $this->assertObjectHasAttribute('gpgkey', $this->_responseJsonBody);
        $this->assertGpgkeyAttributes($this->_responseJsonBody->gpgkey);
        $this->assertObjectHasAttribute('role', $this->_responseJsonBody);
        $this->assertRoleAttributes($this->_responseJsonBody->role);

        // @todo group users
        // @todo avatar
    }

    public function testUsersViewGetApiV1Success()
    {
        $this->authenticateAs('ada');
        $uuid = Common::uuid('user.id.ada');
        $this->getJson('/users/' . $uuid . '.json');
        $this->assertSuccess();
        $this->assertNotNull($this->_responseJsonBody);

        $this->assertObjectHasAttribute('User', $this->_responseJsonBody);
        $this->assertUserAttributes($this->_responseJsonBody->User);
        $this->assertObjectHasAttribute('Profile', $this->_responseJsonBody);
        $this->assertProfileAttributes($this->_responseJsonBody->Profile);
        $this->assertObjectHasAttribute('Gpgkey', $this->_responseJsonBody);
        $this->assertGpgkeyAttributes($this->_responseJsonBody->Gpgkey);
        $this->assertObjectHasAttribute('Role', $this->_responseJsonBody);
        $this->assertRoleAttributes($this->_responseJsonBody->Role);

        // @todo group users
        // @todo avatar
    }

    public function testUsersViewGetMeSuccess()
    {
        $this->authenticateAs('ada');
        $uuid = Common::uuid('user.id.ada');
        $this->getJson('/users/me.json');
        $this->assertSuccess();
        $this->assertNotNull($this->_responseJsonBody);

        $this->assertObjectHasAttribute('User', $this->_responseJsonBody);
        $this->assertUserAttributes($this->_responseJsonBody->User);
        $this->assertEquals($this->_responseJsonBody->User->id, $uuid);
    }

    public function testUsersViewNotLoggedInError() {
        $this->getJson('/users/me.json');
        $this->assertAuthenticationError();
    }

    public function testUsersViewInvalidIdError() {
        $this->authenticateAs('ada');
        $this->getJson('/users/notuuid.json');
        $this->assertError(400, 'The user id should be a uuid or "me".');
    }

    public function testUsersViewUserDoesNotExistError() {
        $this->authenticateAs('ada');
        $uuid = Common::uuid('user.id.notauser');
        $this->getJson('/users/' . $uuid . '.json');
        $this->assertError(404, 'The user does not exist.');
    }
}