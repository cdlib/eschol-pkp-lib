<?php

/**
 * @file controllers/grid/settings/reviewForms/ReviewFormGridHandler.inc.php 
 *
 * Copyright (c) 2000-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormGridHandler
 * @ingroup controllers_grid_settings_reviewForms
 *
 * @brief Handle review form grid requests.
 */

import('lib.pkp.controllers.grid.settings.reviewForms.PKPReviewFormGridHandler');
import('lib.pkp.controllers.grid.settings.reviewForms.ReviewFormGridRow');

import('lib.pkp.controllers.grid.settings.reviewForms.form.PreviewReviewForm');

class ReviewFormGridHandler extends PKPReviewFormGridHandler {
	/**
	 * Constructor
	 */
	function ReviewFormGridHandler() {
		parent::PKPReviewFormGridHandler();
	}


	//
	// Implement template methods from PKPHandler.
	//
	/**
	 * @see PKPHandler::initialize()
	 */
	function initialize($request) {
		// Load user-related translations.
		AppLocale::requireComponents(
			LOCALE_COMPONENT_APP_ADMIN,
			LOCALE_COMPONENT_APP_MANAGER,
			LOCALE_COMPONENT_APP_COMMON
		);

		parent::initialize($request);

		// Basic grid configuration.
		$this->setTitle('manager.reviewForms');
	}


	//
	// Implement methods from GridHandler.
	//
	/**
	 * @see GridHandler::getRowInstance()
	 * @return UserGridRow
	 */
	function getRowInstance() {
		return new ReviewFormGridRow();
	}

	/**
	 * @see GridHandler::loadData()
	 * @param $request PKPRequest
	 * @return array Grid data.
	 */
	function loadData($request) {
                // Get all review forms.
                $reviewFormDao = DAORegistry::getDAO('ReviewFormDAO');
		$journal = $request->getJournal();
		$journalId = $journal->getId();
                $reviewForms = $reviewFormDao->getByAssocId(ASSOC_TYPE_JOURNAL, $journalId);

                return $reviewForms->toAssociativeArray();
	}

	/**
	 * @see lib/pkp/classes/controllers/grid/GridHandler::setDataElementSequence()
	 */
	function setDataElementSequence($request, $rowId, &$reviewForm, $newSequence) {
                $reviewFormDao = DAORegistry::getDAO('ReviewFormDAO'); /* @var $reviewFormDao ReviewFormDAO */
                $reviewForm->setSequence($newSequence);
                $reviewFormDao->updateObject($reviewForm);
	}


	//
	// Public grid actions.
	//

	function previewReviewForm($args, $request) {
                // Identify the review form Id
                $reviewFormId = $request->getUserVar('rowId');

                // Display 'editReviewForm' tabs 
                $templateMgr = TemplateManager::getManager($request);
                $templateMgr->assign('reviewFormId', $reviewFormId);
                $json = new JSONMessage(true, $templateMgr->fetch('controllers/grid/settings/reviewForms/editReviewForm.tpl'));
                return $json->getString();	
	}

        /**
         * Preview a review form.
         * @param $args array
         * @param $request PKPRequest
         * @return string Serialized JSON object
         */
	function reviewFormPreview($args, $request) {
		// Identify the review form Id.
		$reviewFormId = $request->getUserVar('reviewFormId');

                // Identify the context id.
                $context = $request->getContext();
                $contextId = $context->getId();

                // Get review form object
                $reviewFormDao = DAORegistry::getDAO('ReviewFormDAO');
                $reviewForm = $reviewFormDao->getReviewForm($reviewFormId, ASSOC_TYPE_JOURNAL, $contextId);	

		// If no review form Id, then kick user back to main Review Forms page.
		// FIXME figure out how to do this under new js schema
                if (!isset($reviewForm)) {
                        #Request::redirect(null, null, 'reviewForms');
                }

		// Determine whether or not the review form is in use. If it is, then we don't want the user to modify it.
		/***
                $completeCounts = $reviewFormDao->getUseCounts(ASSOC_TYPE_JOURNAL, $contextId, true);
                $incompleteCounts = $reviewFormDao->getUseCounts(ASSOC_TYPE_JOURNAL, $contextId, false);
		if ($completeCounts[$reviewFormId] != 0 || $incompleteCounts[$reviewFormId] != 0) {
			$inUse = 1;
		} else {
			$inUse = 0;
		}
		***/

		// Form handling
		#$previewReviewForm = new PreviewReviewForm($inUse ? null : $reviewFormId);
		$previewReviewForm = new PreviewReviewForm($reviewFormId);
		$previewReviewForm->initData();
		$json = new JSONMessage(true, $previewReviewForm->fetch($args, $request));

                return $json->getString();
	}

        /**
         * Update an existing review form.
         * @param $args array
         * @param $request PKPRequest
         * @return string Serialized JSON object
         */
	function updateReviewForm($args, $request) {
		// Identify the review form Id.
		$reviewFormId = $request->getUserVar('reviewFormId');

		// Identify the context id.
		$context = $request->getContext();
		$contextId = $context->getId();

		// Get review form object
		$reviewFormDao = DAORegistry::getDAO('ReviewFormDAO');
		$reviewForm = $reviewFormDao->getReviewForm($reviewFormId, ASSOC_TYPE_JOURNAL, $contextId);	

		// Form handling.
		$reviewFormForm = new ReviewFormForm(!isset($reviewFormId) || empty($reviewFormId) ? null : $reviewFormId);
		$reviewFormForm->readInputData();

		if ($reviewFormForm->validate()) {
			$reviewFormForm->execute($request);

                        // Create the notification.
                        $notificationMgr = new NotificationManager();
                        $user = $request->getUser();
                        $notificationMgr->createTrivialNotification($user->getId());

                        return DAO::getDataChangedEvent($reviewFormId);

		}

		$json = new JSONMessage(false);
		return $json->getString();
	}

        /**
         * Copy a review form.
         * @param $args array
         * @param $request PKPRequest
         * @return string Serialized JSON object
         */
	function copyReviewForm($args, $request) {
                // Identify the current review form
                $reviewFormId = $request->getUserVar('rowId');

               // Identify the context id.
                $context = $request->getContext();
                $contextId = $context->getId();

                // Get review form object
                $reviewFormDao = DAORegistry::getDAO('ReviewFormDAO');
                $reviewForm = $reviewFormDao->getReviewForm($reviewFormId, ASSOC_TYPE_JOURNAL, $contextId);

                if (isset($reviewForm)) {
                        $reviewForm->setActive(0);
                        $reviewForm->setSequence(REALLY_BIG_NUMBER);
                        $newReviewFormId = $reviewFormDao->insertObject($reviewForm);
                        $reviewFormDao->resequenceReviewForms(ASSOC_TYPE_JOURNAL, $contextId);

                        $reviewFormElementDao =& DAORegistry::getDAO('ReviewFormElementDAO');
                        $reviewFormElements =& $reviewFormElementDao->getReviewFormElements($reviewFormId);
                        foreach ($reviewFormElements as $reviewFormElement) {
                                $reviewFormElement->setReviewFormId($newReviewFormId);
                                $reviewFormElement->setSequence(REALLY_BIG_NUMBER);
                                $reviewFormElementDao->insertObject($reviewFormElement);
                                $reviewFormElementDao->resequenceReviewFormElements($newReviewFormId);
                        }

                        // Create the notification.
                        $notificationMgr = new NotificationManager();
                        $user = $request->getUser();
                        $notificationMgr->createTrivialNotification($user->getId());

                        return DAO::getDataChangedEvent($reviewFormId);
			# FIXME The new new row doesn't appear at the bottom of the grid w/o refreshing page manually
                }

		$json = new JSONMessage(false);
                return $json->getString();
	}

        /**
         * Activate a review form.
         * @param $args array
         * @param $request PKPRequest
         * @return string Serialized JSON object
         */
        function activateReviewForm($args, $request) {
                // Identify the current review form
                $reviewFormId = $request->getUserVar('reviewFormKey');

                // Identify the context id.
                $context = $request->getContext();
                $contextId = $context->getId();

		// Get review form object
		$reviewFormDao = DAORegistry::getDAO('ReviewFormDAO');
                $reviewForm = $reviewFormDao->getReviewForm($reviewFormId, ASSOC_TYPE_JOURNAL, $contextId);

		if (isset($reviewForm) && !$reviewForm->getActive()) {
                        $reviewForm->setActive(1);
                        $reviewFormDao->updateObject($reviewForm);

			// Create the notification.
                        $notificationMgr = new NotificationManager();
                        $user = $request->getUser();
                        $notificationMgr->createTrivialNotification($user->getId());

                        return DAO::getDataChangedEvent($reviewFormId);
                }
		// FIXME catch exceptions?

		$json = new JSONMessage(false);
                return $json->getString();
	}


        /**
         * Deactivate a review form.
         * @param $args array
         * @param $request PKPRequest
         * @return string Serialized JSON object
         */
        function deactivateReviewForm($args, $request) {

		// Identify the current review form
                $reviewFormId = $request->getUserVar('reviewFormKey');

                // Identify the context id.
                $context = $request->getContext();
                $contextId = $context->getId();

                // Get review form object
                $reviewFormDao = DAORegistry::getDAO('ReviewFormDAO');
                $reviewForm = $reviewFormDao->getReviewForm($reviewFormId, ASSOC_TYPE_JOURNAL, $contextId);

                if (isset($reviewForm) && $reviewForm->getActive()) {
			$reviewForm->setActive(0);
                        $reviewFormDao->updateObject($reviewForm);

                        // Create the notification.
                        $notificationMgr = new NotificationManager();
                        $user = $request->getUser();
                        $notificationMgr->createTrivialNotification($user->getId());

                        return DAO::getDataChangedEvent($reviewFormId);
		}
		// FIXME catch exceptions?

                $json = new JSONMessage(false);
                return $json->getString();
	}

        /**
         * Delete a review form.
         * @param $args array
         * @param $request PKPRequest
         * @return string Serialized JSON object
         */
        function deleteReviewForm($args, $request) {
                // Identify the current review form
		$reviewFormId = $request->getUserVar('rowId');

		// Identify the context id.
                $context = $request->getContext();
                $contextId = $context->getId();

                // Get review form object
                $reviewFormDao = DAORegistry::getDAO('ReviewFormDAO');
                $reviewForm = $reviewFormDao->getReviewForm($reviewFormId, ASSOC_TYPE_JOURNAL, $contextId);

                $completeCounts = $reviewFormDao->getUseCounts(ASSOC_TYPE_JOURNAL, $contextId, true);
                $incompleteCounts = $reviewFormDao->getUseCounts(ASSOC_TYPE_JOURNAL, $contextId, false);
		
		if (isset($reviewForm) && $completeCounts[$reviewFormId] == 0 && $incompleteCounts[$reviewFormId] == 0) {
                        $reviewAssignmentDao =& DAORegistry::getDAO('ReviewAssignmentDAO');
                        $reviewAssignments =& $reviewAssignmentDao->getByReviewFormId($reviewFormId);

                        foreach ($reviewAssignments as $reviewAssignment) {
                                $reviewAssignment->setReviewFormId('');
                                $reviewAssignmentDao->updateReviewAssignment($reviewAssignment);
                        }

                        $reviewFormDao->deleteById($reviewFormId, $contextId);
			
			// Create the notification.
                        $notificationMgr = new NotificationManager();
                        $user = $request->getUser();
                        $notificationMgr->createTrivialNotification($user->getId());

			return DAO::getDataChangedEvent($reviewFormId);	
                }

		$json = new JSONMessage(false);
		return $json->getString();	
        }


	//
	// Private helper methods.
	//
	/**
	 * Get the "add review form" locale key
	 * @return string
	 */
	protected function _getAddReviewFormKey() {
		return 'manager.reviewForms.create';
	}

	/**
	 * Get the review form name locale key
	 * @return string
	 */
	protected function _getReviewFormNameKey() {
		return 'manager.reviewForms.title';
	}
}

?>
