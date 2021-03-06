<?php

/**
 * @file tests/plugins/metadata/mods/filter/ModsDescriptionTestCase.inc.php
 *
 * Copyright (c) 2000-2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ModsDescriptionTestCase
 * @ingroup tests_plugins_metadata_mods_filter
 * @see ModsSchema
 *
 * @brief Base test case for tests that involve a MODS MetadataDescription.
 */

import('lib.pkp.tests.PKPTestCase');
import('lib.pkp.classes.metadata.MetadataDescription');

class ModsDescriptionTestCase extends PKPTestCase {
	/**
	 * Prepare a MODS description that covers as much data as possible.
	 * @return MetadataDescription
	 */
	public function getModsDescription() {
		// Author
		$authorDescription = new MetadataDescription('lib.pkp.plugins.metadata.mods.schema.ModsNameSchema', ASSOC_TYPE_AUTHOR);
		self::assertTrue($authorDescription->addStatement('[@type]', $nameType = 'personal'));
		self::assertTrue($authorDescription->addStatement('namePart[@type="family"]', $familyName = 'some family name'));
		self::assertTrue($authorDescription->addStatement('namePart[@type="given"]', $givenName = 'given names'));
		self::assertTrue($authorDescription->addStatement('namePart[@type="termsOfAddress"]', $terms = 'Jr'));
		self::assertTrue($authorDescription->addStatement('namePart[@type="date"]', $date = '1900-1988'));
		self::assertTrue($authorDescription->addStatement('affiliation', $affiliation = 'affiliation'));
		self::assertTrue($authorDescription->addStatement('role/roleTerm[@type="code" @authority="marcrelator"]', $authorRole = 'aut'));

		// Sponsor
		$sponsorDescription = new MetadataDescription('lib.pkp.plugins.metadata.mods.schema.ModsNameSchema', ASSOC_TYPE_AUTHOR);
		self::assertTrue($sponsorDescription->addStatement('[@type]', $nameType = 'corporate'));
		self::assertTrue($sponsorDescription->addStatement('namePart', $namePart = 'Some Sponsor'));
		self::assertTrue($sponsorDescription->addStatement('role/roleTerm[@type="code" @authority="marcrelator"]', $sponsorRole = 'spn'));

		$modsDescription = new MetadataDescription('plugins.metadata.mods.schema.ModsSchema', ASSOC_TYPE_CITATION);
		self::assertTrue($modsDescription->addStatement('titleInfo/nonSort', $titleNonSort = 'the'));
		self::assertTrue($modsDescription->addStatement('titleInfo/title', $title = 'new submission title'));
		self::assertTrue($modsDescription->addStatement('titleInfo/subTitle', $subTitle = 'subtitle'));
		self::assertTrue($modsDescription->addStatement('titleInfo/partNumber', $partNumber = 'part I'));
		self::assertTrue($modsDescription->addStatement('titleInfo/partName', $partName = 'introduction'));

		self::assertTrue($modsDescription->addStatement('titleInfo/nonSort', $titleNonSort = 'ein', 'de_DE'));
		self::assertTrue($modsDescription->addStatement('titleInfo/title', $title = 'neuer Titel', 'de_DE'));
		self::assertTrue($modsDescription->addStatement('titleInfo/subTitle', $subTitle = 'Subtitel', 'de_DE'));
		self::assertTrue($modsDescription->addStatement('titleInfo/partNumber', $partNumber = 'Teil I', 'de_DE'));
		self::assertTrue($modsDescription->addStatement('titleInfo/partName', $partName = 'Einführung', 'de_DE'));

		self::assertTrue($modsDescription->addStatement('name', $authorDescription));
		self::assertTrue($modsDescription->addStatement('name', $sponsorDescription));

		self::assertTrue($modsDescription->addStatement('typeOfResource', $typeOfResource = 'text'));

		self::assertTrue($modsDescription->addStatement('genre[@authority="marcgt"]', $marcGenre = 'book'));

		self::assertTrue($modsDescription->addStatement('originInfo/place/placeTerm[@type="text"]', $publisherPlace = 'Vancouver'));
		self::assertTrue($modsDescription->addStatement('originInfo/place/placeTerm[@type="code" @authority="iso3166"]', $publisherCountry = 'CA'));
		self::assertTrue($modsDescription->addStatement('originInfo/publisher', $publisherName = 'Public Knowledge Project'));
		self::assertTrue($modsDescription->addStatement('originInfo/dateIssued[@keyDate="yes" @encoding="w3cdtf"]', $publicationDate = '2010-09'));
		self::assertTrue($modsDescription->addStatement('originInfo/dateCreated[@encoding="w3cdtf"]', $publisherName = '2010-07-07'));
		self::assertTrue($modsDescription->addStatement('originInfo/copyrightDate[@encoding="w3cdtf"]', $publisherName = '2010'));
		self::assertTrue($modsDescription->addStatement('originInfo/edition', $edition = 'second revised edition'));
		self::assertTrue($modsDescription->addStatement('originInfo/edition', $edition = 'zweite überarbeitete Ausgabe', 'de_DE'));

		self::assertTrue($modsDescription->addStatement('language/languageTerm[@type="code" @authority="iso639-2b"]', $submissionLanguage = 'eng'));

		self::assertTrue($modsDescription->addStatement('physicalDescription/form[@authority="marcform"]', $publicationForm = 'electronic'));
		self::assertTrue($modsDescription->addStatement('physicalDescription/internetMediaType', $mimeType = 'application/pdf'));
		self::assertTrue($modsDescription->addStatement('physicalDescription/extent', $pages = 215));

		self::assertTrue($modsDescription->addStatement('abstract', $abstract1 = 'some abstract'));
		self::assertTrue($modsDescription->addStatement('abstract', $abstract2 = 'eine Zusammenfassung', 'de_DE'));

		self::assertTrue($modsDescription->addStatement('note', $note1 = 'some note'));
		self::assertTrue($modsDescription->addStatement('note', $note2 = 'another note'));
		self::assertTrue($modsDescription->addStatement('note', $note3 = 'übersetzte Anmerkung', 'de_DE'));

		self::assertTrue($modsDescription->addStatement('subject/topic', $topic1 = 'some subject'));
		self::assertTrue($modsDescription->addStatement('subject/topic', $topic2 = 'some other subject'));
		self::assertTrue($modsDescription->addStatement('subject/topic', $topic3 = 'ein Thema', 'de_DE'));
		self::assertTrue($modsDescription->addStatement('subject/geographic', $geography = 'some geography'));
		self::assertTrue($modsDescription->addStatement('subject/temporal[@encoding="w3cdtf" @point="start"]', $timeStart = '1950'));
		self::assertTrue($modsDescription->addStatement('subject/temporal[@encoding="w3cdtf" @point="end"]', $timeEnd = '1954'));

		self::assertTrue($modsDescription->addStatement('identifier[@type="isbn"]', $isbn = '01234567890123'));
		self::assertTrue($modsDescription->addStatement('identifier[@type="doi"]', $doi = '40/2010ff'));
		self::assertTrue($modsDescription->addStatement('identifier[@type="uri"]', $uri = 'urn://xyz.resolver.org/12345'));

		self::assertTrue($modsDescription->addStatement('location/url[@usage="primary display"]', $url = 'http://www.sfu.ca/test-article'));

		self::assertTrue($modsDescription->addStatement('recordInfo/recordCreationDate[@encoding="w3cdtf"]', $recordDate = '2010-12-24'));
		self::assertTrue($modsDescription->addStatement('recordInfo/recordIdentifier[@source="pkp"]', $articleId = '3049'));
		self::assertTrue($modsDescription->addStatement('recordInfo/languageOfCataloging/languageTerm[@authority="iso639-2b"]', $languageOfCataloging = 'eng'));

		return $modsDescription;
	}
}
?>