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
 * @method getSample Return a sample for the given email template.
 * The sample will typically be
 * an HTML template.
 */
class Mail extends \Presentation\Component {


	// If no subject is provided, 
	// this value will be used.
	private static $DEFAULT_SUBJECT = "OCDLA CAR notifications";


	// If no court is selected, this value 
	// will be used in queries and in the title.
	private static $DEFAULT_COURT = "Oregon Court of Appeals";


	// Heading that appears at the top of the email.
	// Params: court and selected date.
	private static $DEFAULT_TITLE = "Appellate Review - %s, %s";


	// List of templates, keyed by name,
	// provided by this 
	private $templates = array(
		"notification" 	=> array(
			"name" => "CAR Notification",
			"subject" => "OCDLA CAR notifications",
			"title" => "OCDLA CAR notifications"
		)
	);



	public function __construct() {
		parent::__construct("mail");
		
	}



	public function getTemplates() {

		return $this->templates;
	}


	
	public function getCustomFields() {

		$form = new \Template("custom-fields");
		$form->addPath(__DIR__ . "/templates");

		return $form->render();
	}








	public function getSubject($params) {

		return self::$DEFAULT_SUBJECT;
	}



	public function getTitle($params) {

		$court = empty($params->court) ? self::$DEFAULT_COURT : $params->court;
		$begin = empty($params->startDate) ? new \DateTime("2022-01-01") : new \DateTime($params->startDate);
		$end = empty($params->endDate) ? new \DateTime() : new \DateTime($params->endDate);
		$abbrvcourt = "Oregon Supreme Court" == $court ? "OSC" : "COA";

		return sprintf(self::$DEFAULT_TITLE, $abbrvcourt, $begin->format('F j, Y'));
	}




	public function getTextBody($params) {

		return "Hello World!";
	}



	public function getHtmlBody($params) {


		$court = empty($params->court) ? self::$DEFAULT_COURT : $params->court;
		$begin = empty($params->startDate) ? new \DateTime("2022-01-01") : new \DateTime($params->startDate);
		$end = empty($params->endDate) ? new \DateTime() : new \DateTime($params->endDate);

		$cars = $this->getRecentCarList($court, $begin, $end);

		// var_dump($cars);exit;
		$list = new \Template("email-list");
		$list->addPath(__DIR__ . "/templates");

		$html = $list->render(["cars" => $cars]);

		
		$body = new \Template("email-body");
		$body->addPath(__DIR__ . "/templates");

		$params = [
			"year" 				=> $begin->format('Y'),
			"decision_date"		=> $begin->format('Ymd'),
			"date" 				=> $begin->format('l, M j  Y'),
			"list" 				=> $html,
			"court" 			=> $court,
			"title"				=> $title
		];

		return $body->render($params);
	}














	public function getRecentCarList($court = "Oregon Court of Appeals", \DateTime $begin = null, \DateTime $end = null) {
		$begin = null == $begin ? new \DateTime() : $begin;
		
		$beginMysql = $begin->format('Ymd');

		if(null == $end) {
			$query = "SELECT * FROM car WHERE decision_date = {$beginMysql}";
			$query .= " AND court = '{$court}'";
		} else {
			$endMysql = $end->format('Ymd');
	
			$query = "SELECT * FROM car WHERE decision_date >= {$beginMysql}";
			$query .= " AND decision_date <= {$endMysql}";
			$query .= " AND court = '{$court}'";
		}

		return select($query);
	}







}