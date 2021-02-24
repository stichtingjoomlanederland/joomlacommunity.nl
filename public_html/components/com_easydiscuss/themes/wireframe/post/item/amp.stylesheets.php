<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');
?>
<style amp-custom>
:root {
	--si-font-size--01: .75rem;
	--si-font-size--02: .875rem;
	--si-font-size--03: 1rem;
	--si-font-size--04: 1.125rem;
	--si-font-size--05: 1.25rem;
	--si-basic--100: #ffffff;
	--si-basic--200: #f9f9fa;
	--si-basic--300: #f0f0f1;
	--si-basic--400: #d9d9de;
	--si-basic--500: #888888;
	--si-basic--600: #494949;
	--si-basic--700: #323232;
	--si-basic--800: #292929;
	--si-basic--900: #1d1d1d;
	--si-answer: #e6f8ef;
	--si-ui-radius: .25rem;
	--si-spacing: 1rem;
	--si-spacing--xs: calc( var(--si-spacing) * 0.5);
	--si-spacing--sm: calc( var(--si-spacing) * 0.75);
	--si-spacing--md: calc( var(--si-spacing) * 1);
	--si-spacing--lg: calc( var(--si-spacing) * 1.5);
	--si-ui-link: #4e72e2;
}
body {
	font-family: 'Heebo', sans-serif;
	font-size: 1rem;
	line-height: 1.6;
	color: var(--si-basic--700);
}
header {
	background-color: var(--si-basic--900);
	color: var(--si-basic--100);
	padding: var(--si-spacing);
	position: relative;
	
}
a {
	color: var(--si-ui-link);
	text-decoration: none;
}
.ed-comment-item-meta {
	padding-top: 10px;
}
.ed-comment-item-meta > div:not(:first-child) {
	margin-left: var(--si-spacing--md);
	padding-left: var(--si-spacing--md);
	border-left: 1px dotted var(--si-basic--400);
}
.ed-main-wrapper {
	padding: var(--si-spacing--md);
}
.ed-main-wrapper > * + * {
	margin-top: var(--si-spacing--md);
}
.brand-logo {
	display: flex;
	align-items: center;
}
.brand-logo amp-img{
	border-radius: 3px;
}
.brand-logo__text {
	margin-left: .5rem;
}
.heading > h1 > a {
	text-decoration: none;
	
}
.toggle-btn {
	position: absolute;
	color: #fff;
	top: 8px;
	<?php echo $isRtl ? 'left: 8px;' : 'right: 8px;'; ?>
}

h1 {
	font-size: 30px;
}
h2 {
	font-size: 26px;
}
h3 {
	font-size: 22px;
}
h4 {
	font-size: 20px;
}
h1, h2, h3, h4 {
	margin-bottom: 10px;
	padding: 0 0 10px;
}

p {	
	color: #222;
	background-color: #fff;
	margin-bottom: 10px;
	padding: 0 0 10px;
}
hr {
	margin-top: 18px;
	margin-bottom: 18px;
	border: 0;
	border-top: 1px solid #eeeeee;
}
table {
	table-layout: fixed;
	border-collapse: collapse;
	border-spacing: 0;
	font-size: 16px;
}
.table td {
	vertical-align: top;
	padding: 6px;
}
.table-bordered {
	border: 1px solid #ddd;
}
.table-bordered > thead > tr > th,
.table-bordered > thead > tr > td,
.table-bordered > tbody > tr > th,
.table-bordered > tbody > tr > td,
.table-bordered > tfoot > tr > th,
.table-bordered > tfoot > tr > td {
  border: 1px solid #ddd;
}
.table-bordered > thead > tr > th,
.table-bordered > thead > tr > td {
  border-bottom-width: 2px;
}
.table-striped > tbody > tr:nth-child(odd) > td,
.table-striped > tbody > tr:nth-child(odd) > th {
  background-color: #f9f9f9;
}
amp-sidebar {
	width: 150px;
	position: relative;
	background: #fff;
	
}
amp-sidebar .close-btn {
	font-size: 30px;
	position: absolute;
	right: 0;
	top: 0;
	width: 40px;
	height: 30px;
	line-height: 1;
	padding: 0;
}
.sidebar-nav {
	position: relative;
	top: 40px;
}
nav ul {
	margin: 0;
	padding: 0;
	border-top: 1px solid #ccc;
}
nav li {
	list-style: none;
	padding: 0;
	margin: 0;
}
nav li a {
	padding: 10px;
	text-decoration: none;
	color: #666666;
	background: #fff;
	border-bottom: 1px solid #ccc;
	display: block;
	font-size: 14px;
}
.ed-post-heading {
}
.ed-post-heading > * + * {
	margin-top: var(--si-spacing--sm);
}
.o-title {
	font-size: var(--si-font-size--04);
	text-decoration: bold;
	word-break: break-word;
}
.o-title,
.o-title a {
	color: var(--si-ui-link);
	text-decoration: none;
}

.ed-post-content {
	border-radius: var(--si-ui-radius);
	background-color: var(--si-basic--200);

}
.ed-post-content > * + * {
	border-top: 1px solid var(--si-basic--300);
}
.ed-post-content__body {
	padding: var(--si-spacing--sm) var(--si-spacing--md);
}

.ed-reply-item {
	border-radius: var(--si-ui-radius);
	background-color: var(--si-basic--200);
}
.ed-reply-item.is-answer {
	background-color: var(--si-answer);
}
.ed-reply-item > * + * {
	border-top: 1px solid var(--si-basic--300);
}
.ed-reply-item__body {
	padding: var(--si-spacing--sm) var(--si-spacing--md);
}

.ed-reply-item .ed-reply-item-content,
.ed-comment-item .ed-comment-item-content{
	word-break: break-word;
}

.ed-content {
	padding: 16px;
}
.o-meta {
	font-size: var(--si-font-size--02);
	color: var(--si-basic--500);
	word-break: break-all;
}

.ed-comment-item-meta,
.ed-reply-item-meta {
	display: flex;
	flex-wrap: wrap;
	font-size: 13px;
	color: var(--si-basic--500);
}
.ed-comment-item-meta > * + *,
.ed-reply-item-meta > * + * {
	margin-left: var(--si-spacing--xs);
}
.ed-assignee a, .ed-reply-item a, .ed-comment-item a{
	text-decoration: none;
	
}

.ed-meta__author {
	margin-bottom: 8px;
}
.ed-comment-list > * + * {
	margin-top: var(--si-spacing--md);
	padding-top: var(--si-spacing--sm);
	border-top: 1px solid var(--si-basic--300);
}

.btn-ed {
	display: inline-block;
	margin-bottom: 0;
	font-weight: normal;
	text-align: center;
	vertical-align: middle;
	background-image: none;
	border: 1px solid transparent;
	white-space: nowrap;
	padding: 6px 12px;
	font-size: 14px;
	line-height: 1.428571429;
	border-radius: 4px;
	text-decoration: none;
	-webkit-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	-o-user-select: none;
	user-select: none;

	color: var(--si-basic--600);
	background-color: #fff;
	border-color: var(--si-basic--400);
}

.btn-ed-view-more {
	margin-top: var(--si-spacing--lg);
	display: block;
}

.ed-social {
	display: flex;
}
.ed-social > * + * {
	margin-left: .5rem;
}
.ed-social > amp-social-share {
	border-radius: 5px;
}

.ed-amp-content {
	word-break: break-word;
}

.ed-counter-title {
	color: #888888;
}
.ed-assignee {
	font-size: 13px;
	margin-bottom: var(--si-spacing--sm);
}
.ed-admin-bar {
	display: flex;
	font-size: 13px;
	flex-wrap: wrap;
}
.ed-admin-bar > div {
	display: inline-flex;
	flex-wrap: wrap;
	padding: 0 var(--si-spacing) 0 0;
}
.ed-admin-bar > div > span {
	margin-right: .2rem;
}
.ed-reply-list {
	position: relative;
}
.ed-reply-list > * + * {
	margin-top: var(--si-spacing--md);
}
.ed-reply-list:before {
	content: '';
	display: block;
	width: 2px;
	height: 100%;
	top: -1rem;
	left: .895rem;
	position: absolute;
	background-color: var(--si-basic--300);
	z-index: -1;
}

.ed-quotes,
blockquote {
	position: relative;
	background-color: var(--si-basic--100);
	border: 1px solid var(--si-basic--300);
	border-radius: 1rem;
	box-shadow: 0.625rem 0.625rem 0 var(--si-basic--300);
	padding: calc( var(--si-spacing) * 1);
	margin-bottom: calc( var(--si-spacing) * 1);
	font-size: var(--si-font-size--02); 
}

.ed-quotes__from {
	font-size: var(--si-font-size--02);
	margin-bottom: calc( var(--si-spacing) * 1);
}
.ed-quotes__from a {
	color: var(--si-basic--500); 
}

.ed-quotes__from span {
	color: var(--si-ui-link); 
}

.ed-quotes__content {
	font-size: var(--si-font-size--02); 
}

amp-addthis[data-widget-type=floating]{
	height: 100%;
}

</style>