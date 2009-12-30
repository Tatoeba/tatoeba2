<?php
$oXML = new XMLWriter();
$oXML->openURI('php://output');

$oXML->startDocument('1.0', 'utf-8');

$oXML->startElement('tatoeba');
$oXML->writeAttribute('version', '1.0');

	$oXML->startElement('error');
		$oXML->writeAttribute('code', $code);
		$oXML->writeAttribute('message', $message);
	$oXML->endElement();

$oXML->endElement();

$oXML->endDocument();

$oXML->flush();
?>