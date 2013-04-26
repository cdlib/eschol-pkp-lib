{**
 * templates/manager/reviewForms/reviewForms.tpl
 *
 * Copyright (c) 2003-2013 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display list of journals in site administration.
 *
 *}
{strip}
{assign var="pageTitle" value="manager.reviewForms"}
{include file="common/header.tpl"}
{/strip}

{url|assign:reviewFormsUrl router=$smarty.const.ROUTE_COMPONENT component="grid.settings.reviewForms.ReviewFormGridHandler" op="fetchGrid"}
{load_url_in_div id="reviewFormGridContainer" url=$reviewFormsUrl}

{include file="common/footer.tpl"}
