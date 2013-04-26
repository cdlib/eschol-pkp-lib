<?php
/**
 * @file controllers/grid/settings/reviewForms/form/PKPReviewFormForm.inc.php 
 *
 * Copyright (c) 2003-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PKPReviewFormForm
 * @ingroup controllers_grid_settings_reviewForms_form
 *
 * @brief Form for manager to edit review form.
 */

import('lib.pkp.classes.db.DBDataXMLParser');
import('lib.pkp.classes.form.Form');

class PKPReviewFormForm extends Form {

	/** The ID of the review form being edited */
	var $reviewFormId;

	/**
	 * Constructor.
	 * @param $template string
	 * @param $reviewFormId omit for a new review form
	 */
	function PKPReviewFormForm($template, $reviewFormId = null) {
		parent::Form('manager/reviewForms/reviewFormForm.tpl');

		$this->reviewFormId = isset($reviewFormId) ? (int) $reviewFormId : null;

		// Validation checks for this form
		$this->addCheck(new FormValidatorPost($this));
	}

	/**
	 * Display the form.
	 */
	function fetch($args, $request) {
		$json = new JSONMessage();

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('reviewFormId', $this->reviewFormId);

		return parent::fetch($request);
	}

	/**
	 * Initialize form data from current settings.
	 * @param $reviewForm ReviewForm optional
	 */
	function initData($reviewForm = null) {
		if ($reviewForm) {
			$this->setData('title', $reviewForm->getTitle(null));
			$this->setData('description', $reviewForm->getDescription(null));
		}
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('title', 'description'));
	}

	/**
	 * Get a list of field names for which localized settings are used
	 * @return array
	 */
	function getLocaleFieldNames() {
                $reviewFormDao =& DAORegistry::getDAO('ReviewFormDAO');
                return $reviewFormDao->getLocaleFieldNames();
	}
}

?>
