<?php

/**
 * @file tests/classes/core/StringTest.inc.php
 *
 * Copyright (c) 2000-2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class StringTest
 * @ingroup tests_classes_core
 * @see String
 *
 * @brief Tests for the String class.
 */

import('lib.pkp.tests.PKPTestCase');
import('lib.pkp.classes.core.String');

class StringTest extends PKPTestCase {
	/**
	 * @covers OjsString::titleCase
	 */
	public function testTitleCase() {
		$originalTitle = 'AND This IS A TEST title';
		self::assertEquals('And This is a Test Title', OjsString::titleCase($originalTitle));
	}

	/**
	 * @covers OjsString::trimPunctuation
	 */
	public function testTrimPunctuation() {
		$trimmedChars = array(
			' ', ',', '.', ';', ':', '!', '?',
			'(', ')', '[', ']', '\\', '/'
		);

		foreach($trimmedChars as $trimmedChar) {
			self::assertEquals('trim.med',
					OjsString::trimPunctuation($trimmedChar.'trim.med'.$trimmedChar));
		}
	}

	/**
	 * @covers OjsString::diff
	 */
	public function testDiff() {
		// Test two strings that have common substrings.
		$originalString = 'The original string.';
		$editedString = 'The edited original.';
		$expectedDiff = array(
			array( 0 => 'The'),
			array( 1 => ' edited'),
			array( 0 => ' original'),
			array( -1 => ' string'),
			array( 0 => '.')
		);
		$resultDiff = OjsString::diff($originalString, $editedString);
		self::assertEquals($expectedDiff, $resultDiff);

		// Test two completely different strings.
		$originalString = 'abc';
		$editedString = 'def';
		$expectedDiff = array(
			array( -1 => 'abc'),
			array( 1 => 'def')
		);
		$resultDiff = OjsString::diff($originalString, $editedString);
		self::assertEquals($expectedDiff, $resultDiff);

		// A more realistic example from the citation editor use case
		$originalString = 'Willinsky, B. (2006). The access principle: The case for open acces to research and scholarship. Cambridge, MA: MIT Press.';
		$editedString = 'Willinsky, J. (2006). The access principle: The case for open access to research and scholarship. Cambridge, MA: MIT Press.';
		$expectedDiff = array(
			array( 0 => 'Willinsky, ' ),
			array( -1 => 'B' ),
			array( 1 => 'J' ),
			array( 0 => '. (2006). The access principle: The case for open acce' ),
			array( 1 => 's' ),
			array( 0 => 's to research and scholarship. Cambridge, MA: MIT Press.' )
		);
		$resultDiff = OjsString::diff($originalString, $editedString);
		self::assertEquals($expectedDiff, $resultDiff);
	}
}
?>
