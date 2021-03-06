<?php

/**
 * @file tests/classes/citation/output/abnt/NlmCitationSchemaAbntFilterTest.inc.php
 *
 * Copyright (c) 2000-2011 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class NlmCitationSchemaAbntFilterTest
 * @ingroup tests_classes_citation_output_abnt
 * @see NlmCitationSchemaAbntFilter
 *
 * @brief Tests for the NlmCitationSchemaAbntFilter class.
 */


import('lib.pkp.classes.citation.output.abnt.NlmCitationSchemaAbntFilter');
import('lib.pkp.tests.classes.citation.output.NlmCitationSchemaCitationOutputFormatFilterTest');

class NlmCitationSchemaAbntFilterTest extends NlmCitationSchemaCitationOutputFormatFilterTest {
	/*
	 * Implements abstract methods from NlmCitationSchemaCitationOutputFormatFilter
	 */
	protected function getFilterInstance() {
		return new NlmCitationSchemaAbntFilter();
	}

	protected function getBookResultNoAuthor() {
		return array('<p><i>Mania de bater:</i> A punição corporal doméstica de crianças e adolescentes no Brasil. São Paulo: Iglu, 2001. 368 p. (Edição Standard Brasileira das Obras Psicológicas, v.10)', '</p>');
	}

	protected function getBookResult() {
		return array('<p>AZEVEDO, M.A. <i>Mania de bater:</i> A punição corporal doméstica de crianças e adolescentes no Brasil. São Paulo: Iglu, 2001. 368 p. (Edição Standard Brasileira das Obras Psicológicas, v.10)', '</p>');
	}

	protected function getBookChapterResult() {
		return array('<p>AZEVEDO, M.A.; GUERRA, V. Psicologia genética e lógica. In: ________. <i>Mania de bater:</i> A punição corporal doméstica de crianças e adolescentes no Brasil. São Paulo: Iglu, 2001. 368 p. (Edição Standard Brasileira das Obras Psicológicas, v.10)', '</p>');
	}

	protected function getBookChapterWithEditorResult() {
		return array('<p>AZEVEDO, M.A.; GUERRA, V. Psicologia genética e lógica. In: BANKS-LEITE, L. (Ed.). <i>Mania de bater:</i> A punição corporal doméstica de crianças e adolescentes no Brasil. São Paulo: Iglu, 2001. 368 p. (Edição Standard Brasileira das Obras Psicológicas, v.10)', '</p>');
	}

	protected function getBookChapterWithEditorsResult() {
		return array('<p>AZEVEDO, M.A.; GUERRA, V. Psicologia genética e lógica. In: BANKS-LEITE, L.; VELADO, JR M. (Ed.). <i>Mania de bater:</i> A punição corporal doméstica de crianças e adolescentes no Brasil. São Paulo: Iglu, 2001. 368 p. (Edição Standard Brasileira das Obras Psicológicas, v.10)', '</p>');
	}

	protected function getJournalArticleResult() {
		return array('<p>SILVA, V.A.; DOS SANTOS, P. Etinobotânica Xucuru: espécies místicas. <i>Biotemas</i>, Florianópolis, v.15, n.1, p.45-57, jun 2000. pmid:12140307. doi:10146:55793-493.', '</p>');
	}

	protected function getJournalArticleWithMoreThanSevenAuthorsResult() {
		return array('<p>SILVA, V.A. et al. Etinobotânica Xucuru: espécies místicas. <i>Biotemas</i>, Florianópolis, v.15, n.1, p.45-57, jun 2000. pmid:12140307. doi:10146:55793-493.', '</p>');
	}

	protected function getConfProcResult() {
		$this->markTestIncomplete();
	}
}
?>
