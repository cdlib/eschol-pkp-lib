<?php
# FIXME file info
/**
 * @file classes/controllers/listbuilder/content/navigation/FooterLinkListbuilderGridCellProvider.inc.php
 *
 * Copyright (c) 2000-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class FooterLinkListbuilderGridCellProvider
 * @ingroup controllers_listbuilder_content_navigation
 *
 * @brief Provide labels for footer link listbuilder.
 */

import('lib.pkp.classes.controllers.grid.GridCellProvider');

class ReviewFormElementResponseItemListbuilderGridCellProvider extends GridCellProvider {
	/**
	 * Constructor
	 */
	function ReviewFormElementResponseItemListbuilderGridCellProvider () {
		parent::GridCellProvider();
	}

	//
	// Template methods from GridCellProvider
	//
	/**
	 * @see GridCellProvider::getTemplateVarsFromRowColumn()
	 */
	function getTemplateVarsFromRowColumn($row, $column) {
		$possibleResponse = $row->getData();
		$order = (int)$possibleResponse['order'];
		$content = $possibleResponse['content'];

		$columnId = $column->getId();
		assert(is_int($order) && !empty($columnId)); 

		switch ($columnId) {
			case 'possibleResponse':
				return array('labelKey' => $order, 'label' => $content);
		}
	}
}

?>
