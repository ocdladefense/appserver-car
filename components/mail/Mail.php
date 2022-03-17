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

		
		$user = current_user();
		$req = $this->getRequest();
		$body = $req->getBody();


		$court = empty($body->court) ? "Oregon Court of Appeals" : $body->court;
		$begin = empty($body->startDate) ? new \DateTime("2022-01-01") : new \DateTime($body->startDate);
		$end = empty($body->endDate) ? new \DateTime() : new \DateTime($body->endDate);


		$subject = "OCDLA CAR notifications";
		$to = $user->getEmail();
		$tcourt = "Oregon Supreme Court" == $court ? "OSC" : "COA";
		$title = sprintf("Appellate Review - %s, %s", $tcourt, $begin->format('F j, Y'));




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
			"carList" => $html,
			"court" => $court
		];

	
		return array($subject,$title,$body->render($params));
	}










	public function getRecentCarList($court = "Oregon Court of Appeals", \DateTime $begin = null, \DateTime $end = null) {
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







}