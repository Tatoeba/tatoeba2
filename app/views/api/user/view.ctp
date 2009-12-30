<?php
$oXML = new XMLWriter();
$oXML->openURI('php://output');

$oXML->startDocument('1.0');

$oXML->startElement('tatoeba');
$oXML->writeAttribute('version', '1.0');

	$oXML->startElement('error');
		$oXML->writeAttribute('code', $code);
		$oXML->writeAttribute('message', $message);
	$oXML->endElement();

	$oXML->startElement('user');

		$oXML->startElement('id');
		$oXML->text($user['User']['id']);
		$oXML->endElement();

		$oXML->startElement('username');
		$oXML->text($user['User']['username']);
		$oXML->endElement();

		$oXML->startElement('name');
		$oXML->text($user['User']['name']);
		$oXML->endElement();

		$oXML->startElement('image');
		$oXML->text($user['User']['image']);
		$oXML->endElement();

		$oXML->startElement('url');
		$oXML->text($user['User']['homepage']);
		$oXML->endElement();

		$oXML->startElement('email');
		$oXML->text($user['User']['email']);
		$oXML->endElement();

		$oXML->startElement('birthday');
		$oXML->text($user['User']['birthday']);
		$oXML->endElement();

		$oXML->startElement('description');
			$oXML->writeAttribute('lang', $user['User']['lang']);
			// $oXML->writeAttribute('dir', 'ltr');
		$oXML->text($user['User']['description']);
		$oXML->endElement();

		$oXML->startElement('joined');
		$oXML->text($user['User']['since']);
		$oXML->endElement();

		$oXML->startElement('country');
		$oXML->text($user['Country']['name']);
		$oXML->endElement();

		
		$oXML->startElement('sentences_count');
		$oXML->text(count($user['Sentences']));
		$oXML->endElement();

		$oXML->startElement('comments_count');
		$oXML->text(count($user['SentenceComments']));
		$oXML->endElement();

		$oXML->startElement('favourites_count');
		$oXML->text(count($user['Favorite']));
		$oXML->endElement();

	$oXML->endElement();

$oXML->endElement();

$oXML->endDocument();

$oXML->flush();
?>