<?php
/**
 * @file controllers/grid/settings/reviewForms/PKPReviewFormGridHandler.inc.php
 *
 * Copyright (c) 2000-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PKPReviewFormGridHandler
 * @ingroup controllers_grid_settings_reviewForms
 *
 * @brief Handle review form grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('lib.pkp.controllers.grid.settings.reviewForms.form.ReviewFormForm');
import('lib.pkp.controllers.grid.settings.reviewForms.form.ReviewFormElements');

class PKPReviewFormGridHandler extends GridHandler {
	/**
	 * Constructor
	 */
	function PKPReviewFormGridHandler() {
		parent::GridHandler();
		$this->addRoleAssignment(array(
			ROLE_ID_MANAGER),
			array('fetchGrid', 'fetchRow', 'createReviewForm', 'editReviewForm', 'updateReviewForm',
				'reviewFormBasics', 'reviewFormElements', 'copyReviewForm', 'previewReviewForm', 
				'reviewFormPreview', 'activateReviewForm', 'deactivateReviewForm', 'deleteReviewForm', 
				'saveSequence')
		);
	}


	//
	// Implement template methods from PKPHandler.
	//
	/**
	 * @see PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments) {
		import('lib.pkp.classes.security.authorization.PolicySet');
		$rolePolicy = new PolicySet(COMBINING_PERMIT_OVERRIDES);

		import('lib.pkp.classes.security.authorization.RoleBasedHandlerOperationPolicy');
		foreach($roleAssignments as $role => $operations) {
			$rolePolicy->addPolicy(new RoleBasedHandlerOperationPolicy($request, $role, $operations));
		}
		$this->addPolicy($rolePolicy);

		return parent::authorize($request, $args, $roleAssignments);
	}

	/**
	 * @see PKPHandler::initialize()
	 */
	function initialize($request) {
		parent::initialize($request);

		// Load user-related translations.
		AppLocale::requireComponents(
			LOCALE_COMPONENT_PKP_USER
		);

		// Grid actions.
		$router = $request->getRouter();

		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$this->addAction(
			new LinkAction(
				'createReviewForm',
				new AjaxModal(
					$router->url($request, null, null, 'createReviewForm', null, null),
					__($this->_getAddReviewFormKey()),
					'modal_add_item',
					true
					),
				__($this->_getAddReviewFormKey()),
				'add_item')
		);

		//
		// Grid columns.
		//
		import('lib.pkp.controllers.grid.settings.reviewForms.ReviewFormGridCellProvider');
		$reviewFormGridCellProvider = new ReviewFormGridCellProvider();

		// Review form name.
		$this->addColumn(
			new GridColumn(
				'name',
				$this->_getReviewFormNameKey(),
				null,
				'controllers/grid/gridCell.tpl',
				$reviewFormGridCellProvider
			)
		);

		// Review Form 'in review' 
		$this->addColumn(
			new GridColumn(
				'inReview',
				'manager.reviewForms.inReview',
				null,
				'controllers/grid/gridCell.tpl',
                                $reviewFormGridCellProvider
			)
		);

		// Review Form 'completed'.
		$this->addColumn(
			new GridColumn(
				'completed',
				'manager.reviewForms.completed',
				null,
                                'controllers/grid/gridCell.tpl',
                                $reviewFormGridCellProvider
			)
		);

		// Review form 'activate/deactivate'
		// if ($element->getActive()) {
		$this->addColumn(
			new GridColumn(
				'active', 
				'common.active', 
				null, 
				'controllers/grid/common/cell/selectStatusCell.tpl', 
				$reviewFormGridCellProvider
			)
		);
	}


	//
	// Implement methods from GridHandler.
	//
	/**
	 * @see lib/pkp/classes/controllers/grid/GridHandler::getDataElementSequence()
	 */
	function getDataElementSequence($reviewForm) {
		return $reviewForm->getSequence();
	}

	/**
	 * @see GridHandler::addFeatures()
	 */
	function initFeatures($request, $args) {
		import('lib.pkp.classes.controllers.grid.feature.OrderGridItemsFeature');
		return array(new OrderGridItemsFeature());
	}

	/**
	 * Get the list of "publish data changed" events.
	 * Used to update the site context switcher upon create/delete.
	 * @return array
	 */
	function getPublishChangeEvents() {
		return array('updateHeader');
	}


	//
	// Public grid actions.
	//
        /**
         * Add a new review form.
         * @param $args array
         * @param $request PKPRequest
         */
	function createReviewForm($args, $request) {
		// Form handling.
		$reviewFormForm = new ReviewFormForm(null);
		$reviewFormForm->initData();
		$json = new JSONMessage(true, $reviewFormForm->fetch($args, $request));

		return $json->getString();
	}

	/** 
	 * Edit an existing review form.
	 * @param $args array
	 * @param $request PKPRequest
	 */
	function editReviewForm($args, $request) {
		// Identify the review form Id
		$reviewFormId = $request->getUserVar('rowId');

		// Display 'editReviewForm' tabs
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('reviewFormId', $reviewFormId);
		$json = new JSONMessage(true, $templateMgr->fetch('controllers/grid/settings/reviewForms/editReviewForm.tpl'));
		return $json->getString();
	}

        /**
         * Edit an existing review form's basics (title, description)
         * @param $args array
         * @param $request PKPRequest
         */
	function reviewFormBasics($args, $request) {
                // Identify the review form Id
                $reviewFormId = $request->getUserVar('reviewFormId');

                // Form handling
                $reviewFormForm = new ReviewFormForm(!isset($reviewFormId) || empty($reviewFormId) ? null : $reviewFormId);
                $reviewFormForm->initData();
                $json = new JSONMessage(true, $reviewFormForm->fetch($args, $request));

                return $json->getString();
	}


        /**
         * Display a list of the review form elements within a review form.
         * @param $args array
         * @param $request PKPRequest
         */
	function reviewFormElements($args, $request) {
		// Identify the review form Id
                $reviewFormId = $request->getUserVar('reviewFormId');

		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('reviewFormId', $reviewFormId);
		// get the other info you need
	
		$json = new JSONMessage(true, $templateMgr->fetch('controllers/grid/settings/reviewForms/reviewFormElements.tpl'));	
		return $json->getString();
	}

	//
	// Protected helper methods.
	//
	/**
	 * Get the "add review form" locale key
	 * @return string
	 */
	protected function _getAddReviewFormKey() {
		assert(false); // Should be overridden by subclasses
	}

	/**
	 * Get the context name locale key
	 * @return string
	 */
	protected function _getReviewFormNameKey() {
		assert(false); // Should be overridden by subclasses
	}
}

?>
