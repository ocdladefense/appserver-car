<?php

namespace Car;

use Mysql\Database;
use Mysql\DbHelper;
use Mysql\QueryBuilder;
use Http\HttpRequest;
use Http\HttpHeader;
use Http\HttpHeaderCollection;
use GIS\Political\Countries\US\Oregon;
use Ocdla\Date;


use function Mysql\insert;
use function Mysql\update;
use function Mysql\select;
/**
 * 
 * 
 * 
 * Standard Mail class.
 * 
 * Each Mail class should have methods for:
 * 
 * @method getTemplates Return an array of named templates that can be rendered into an email
 * body.
 * 
 * @method getSample Return a sample for the given email template.  The sample will typically be
 * an HTML template.
 * 
 * 
 */





class Mail extends \Presentation\Component {



	public function __construct() {
		parent::__construct("mail");
		
	}


	public function getTemplates() {

		return array("car-notification" => "CAR Notification");
	}


	
	public function getCustomFields() {

		$form = new \Template("custom-fields");
		$form->addPath(__DIR__ . "/templates");

		return $form->render();
	}





	public function getPreview() {

		$court = "Oregon Court of Appeals";
		$begin = new \DateTime("2022-01-01");
		$end = new \DateTime("2022-03-02");

		$cars = $this->getRecentCarList($court, $begin, $end);

		// var_dump($cars);exit;
		$list = new \Template("email-list");
		$list->addPath(__DIR__ . "/templates");

		$html = $list->render(["cars" => $cars]);

		
		$body = new \Template("email-body");
		$body->addPath(__DIR__ . "/templates");

		$params = [
			"year" => $begin->format('Y'),
			"month" => $begin->format('m'),
			"day" => $begin->format('j'),
			"date" => $begin->format('l, M j  Y'),
			"carList" => $html 
		];

	
		return $body->render($params);
		

		

		// return $this->doMail($params->to, $params->subject, "OCDLA Criminal Appellate Review", $html);


		return $html;
	}







	public function newMail() {

		$params = $this->getRequest()->getBody();

		$startDate = new DateTime($params->startDate);
		$endDate = new DateTime($params->endDate);

		// var_dump($params); exit;

		$cars = $this->getRecentCarList($params->court, $startDate, $endDate);
		

		return $this->doMail($params->to, $params->subject, "OCDLA Criminal Appellate Review", $html);
	}





	public function showMailForm() {

		$today = new DateTime();
		$pickerDate = $today->format("Y-m-d");
		$emailDate = $today->format("M d, Y");

		$form = new Template("car-email-form");
		$form->addPath(__DIR__ . "/templates");

		$params = [
			"defaultEmail"		=> current_user()->getEmail(),
			"defaultSubject"	=> "Appellate Review - COA, $emailDate",
			"defaultPickerDate" => $pickerDate
		];

		return $form->render($params);
	}



	public function getRecentCarList($court = 'Oregon Appellate Court', \DateTime $begin = null, \DateTime $end = null) {
		$begin = null == $begin ? new \DateTime() : $begin;
		
		$beginMysql = $begin->format('Y-m-j');

		if(null == $end) {
			$query = "SELECT * FROM car WHERE decision_date = '{$beginMysql}'";
			$query .= " AND court = '{$court}'";
		} else {
			$endMysql = $end->format('Y-m-j');
	
			$query = "SELECT * FROM car WHERE decision_date >= '{$beginMysql}'";
			$query .= " AND decision_date <= '{$endMysql}'";
			$query .= " AND court = '{$court}'";
		}

		return select($query);
	}





	public function doMail($to, $subject, $title, $content, $headers = array()){

		$headers = [
			"From" 		   => "notifications@ocdla.org",
			"Content-Type" => "text/html"
		];

		$headers = HttpHeaderCollection::fromArray($headers);



		$message = new MailMessage($to);
		$message->setSubject($subject);
		$message->setBody($content);
		$message->setHeaders($headers);
		$message->setTitle($title);

		return $message;
	}



	public function testMail() {


		$to = "jbernal.web.dev@gmail.com";//,rankinjohnsonpdx@gmail.com";
		$subject = "Newest Case Review updates";


		$range = new DateTime("2022-1-10");
		$end = new DateTime();
		$content = $this->getRecentCarList('Oregon Appellate Court', $range, $end);
		

		return $this->doMail($to, $subject, "OCDLA Criminal Appellate Review", $content);
	}


}