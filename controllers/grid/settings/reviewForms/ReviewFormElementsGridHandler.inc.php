<?php

/**
 * @file controllers/grid/settings/reviewForms/ReviewFormElementsGridHandler.inc.php
 *
 * Copyright (c) 2000-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class ReviewFormElementsGridHandler
 * @ingroup controllers_grid_settings_reviewForms
 *
 * @brief Handle review form element grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('lib.pkp.controllers.grid.settings.reviewForms.ReviewFormElementGridRow');
import('lib.pkp.controllers.grid.settings.reviewForms.form.ReviewFormElementForm');

class ReviewFormElementsGridHandler extends GridHandler {
	/**
         * Constructor
         */
        function ReviewFormElementsGridHandler() {
                parent::GridHandler();
                $this->addRoleAssignment(array(
                        ROLE_ID_MANAGER),
			array('fetchGrid', 'fetchRow', 'saveSequence',
				'createReviewFormElement', 'editReviewFormElement', 'deleteReviewFormElement')
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
                        LOCALE_COMPONENT_APP_ADMIN,
                        LOCALE_COMPONENT_APP_MANAGER,
                        LOCALE_COMPONENT_APP_COMMON,
                        LOCALE_COMPONENT_PKP_USER
                );

                // Grid actions.
                $router = $request->getRouter();

                import('lib.pkp.classes.linkAction.request.AjaxModal');
		//
		// Create Review Form Element link
		$reviewFormId = $request->getUserVar('reviewFormId');
                $this->addAction(
                        new LinkAction(
                                'createReviewFormElement',
                                new AjaxModal(
                                        $router->url($request, null, null, 'createReviewFormElement', null, array('reviewFormId' => $reviewFormId)),
                                        __($this->_getAddReviewFormElementKey()),
                                        'modal_add_item',
                                        true
                                        ),
                                __($this->_getAddReviewFormElementKey()),
                                'add_item')
                );


                //
                // Grid columns.
                //
                import('lib.pkp.controllers.grid.settings.reviewForms.ReviewFormElementGridCellProvider');
                $reviewFormElementGridCellProvider = new ReviewFormElementGridCellProvider();

                // Review form element name.
                $this->addColumn(
                        new GridColumn(
                                'question',
                                $this->_getReviewFormElementNameKey(),
                                null,
                                'controllers/grid/gridCell.tpl',
                                $reviewFormElementGridCellProvider
                        )
                );

               // Basic grid configuration.
               $this->setTitle('manager.reviewFormElements');
	}

        //
        // Implement methods from GridHandler.
        //
        /**
         * @see GridHandler::addFeatures()
         */
        function initFeatures($request, $args) {
                import('lib.pkp.classes.controllers.grid.feature.OrderGridItemsFeature');
                return array(new OrderGridItemsFeature());
        }

        /**
         * @see GridHandler::getRowInstance()
         * @return UserGridRow
         */
        function getRowInstance() {
                return new ReviewFormElementGridRow();
        }

        /**
         * @see GridHandler::loadData()
         * @param $request PKPRequest
         * @return array Grid data.
         */
        function loadData($request) {
		// Get review form elements.
		//$rangeInfo = $this->getRangeInfo('reviewFormElements');
		$reviewFormId = $request->getUserVar('reviewFormId');
		$reviewFormElementDao = DAORegistry::getDAO('ReviewFormElementDAO');
		$reviewFormElements = $reviewFormElementDao->getReviewFormElementsByReviewForm($reviewFormId, null); //FIXME add range info?

		return $reviewFormElements->toAssociativeArray();
        }


        /**
         * @see lib/pkp/classes/controllers/grid/GridHandler::setDataElementSequence()
         */
        function setDataElementSequence($request, $rowId, &$reviewForm, $newSequence) {
                $reviewFormElementDao = DAORegistry::getDAO('ReviewFormElementDAO'); /* @var $reviewFormElementDao ReviewFormElementDAO */
                $reviewFormElement->setSequence($newSequence);
                $reviewFormElementDao->updateObject($reviewFormElement);
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
         * Add a new review form element.
        * @param $args array
         * @param $request PKPRequest
         */
        function createReviewFormElement($args, $request) {
		// Identify the review form Id
		$reviewFormId = $request->getUserVar('reviewFormId');

		// Form handling
                $reviewFormElementForm = new ReviewFormElementForm($reviewFormId);
                $reviewFormElementForm->initData();
                $json = new JSONMessage(true, $reviewFormElementForm->fetch($args, $request));

                return $json->getString();
	}

        /**
         * Edit an existing review form element.
        * @param $args array
         * @param $request PKPRequest
         */
        function editReviewFormElement($args, $request) {
		// Identify the review form Id
		$reviewFormId = $request->getUserVar('reviewFormId');

                // Identify the review form element Id
                $reviewFormElementId = $request->getUserVar('rowId');

                // Display form
                $reviewFormElementForm = new ReviewFormElementForm($reviewFormId, $reviewFormElementId);
                $reviewFormElementForm->initData();
                $json = new JSONMessage(true, $reviewFormElementForm->fetch($args, $request));

                return $json->getString();
	}

        //
        // Protected helper methods.
        //
        /**
         * Get the "add review form element" locale key
         * @return string
         */
        protected function _getAddReviewFormElementKey() {
		return 'manager.reviewFormElements.create';
        }

        /**
         * Get the review form element name locale key
         * @return string
         */
        protected function _getReviewFormElementNameKey() {
		return 'manager.reviewFormElements.question';
        }
}

?>
