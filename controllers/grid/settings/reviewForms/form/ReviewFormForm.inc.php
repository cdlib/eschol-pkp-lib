<?php

/**
 * @file controllers/grid/settings/reviewForms/form/ReviewFormForm.inc.php 
 *
 * Copyright (c) 2003-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormForm
 * @ingroup controllers_grid_settings_reviewForms_form
 *
 * @brief Form for manager to edit a review form.
 */

import('lib.pkp.controllers.grid.settings.reviewForms.form.PKPReviewFormForm');

class ReviewFormForm extends PKPReviewFormForm {
	/**
	 * Constructor.
	 * @param $reviewFormId omit for a new review form 
	 */
	function ReviewFormForm($reviewFormId = null) {
		parent::PKPReviewFormForm('manager/reviewForms/reviewFormForm.tpl', $reviewFormId);

		// Validation checks for this form
		$this->addCheck(new FormValidatorLocale($this, 'title', 'required', 'manager.reviewForms.form.titleRequired'));
	}

	/**
	 * Initialize form data from current settings.
	 */
	function initData() {
		if (isset($this->reviewFormId)) {
			$reviewFormDAO = DAORegistry::getDAO('ReviewFormDAO');
			$reviewForm = $reviewFormDAO->getReviewForm($this->reviewFormId, ASSOC_TYPE_JOURNAL, $this->contextId);

			parent::initData($reviewForm);
		} else {
			parent::initData();	
		}
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		parent::readInputData();
	}

	/**
	 * Save review form.
	 * @param $request PKPRequest
	 */
	function execute($request) {
		$context = $request->getContext();
		$contextId = $context->getId();
	
		$reviewFormDao =& DAORegistry::getDAO('ReviewFormDAO');

		$reviewFormId = $this->reviewFormId;

		if (isset($this->reviewFormId)) {
                        $reviewForm = $reviewFormDao->getReviewForm($this->reviewFormId, ASSOC_TYPE_JOURNAL, $contextId);
                }	

		if(!isset($reviewForm)) {
			$reviewForm = $reviewFormDao->newDataObject();
                        $reviewForm->setAssocType(ASSOC_TYPE_JOURNAL);
                        $reviewForm->setAssocId($contextId);
                        $reviewForm->setActive(0);
                        $reviewForm->setSequence(REALLY_BIG_NUMBER);
		}

                $reviewForm->setTitle($this->getData('title'), null); // Localized
                $reviewForm->setDescription($this->getData('description'), null); // Localized

                if ($reviewForm->getId() != null) {
                        $reviewFormDao->updateObject($reviewForm);
                        $reviewFormId = $reviewForm->getId();
                } else {
                        $reviewFormId = $reviewFormDao->insertObject($reviewForm);
                        $reviewFormDao->resequenceReviewForms(ASSOC_TYPE_JOURNAL, $contextId);
                }
	}
}
?>
