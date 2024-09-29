<?php
/**
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @since 0.0.1
 */

namespace MediaWiki\Extension\Adiutor\Tests\Integration\Specials;

use MediaWiki\Extension\Adiutor\Specials\AdiutorSettings;
use MediaWikiIntegrationTestCase;
use OutputPage;

/**
 * @covers \MediaWiki\Extension\Adiutor\Specials\AdiutorSettings
 */
class AdiutorSettingsTest extends MediaWikiIntegrationTestCase {

	public function testExecute() {
		$adiutorSettings = new AdiutorSettings();

		$mockOutputPage = $this->createMock( OutputPage::class );

		$mockOutputPage->expects( $this->once() )
			->method( 'addModules' )
			->with( 'ext.adiutor' );

		$mockOutputPage->expects( $this->once() )
			->method( 'addHTML' )
			->with( $this->isType( 'string' ) );

		$adiutorSettings->setOutput( $mockOutputPage );

		$adiutorSettings->execute( '' );
	}
}
