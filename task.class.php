<?

class Task {

	public $beginDate; //DateTime
	public $endDate; //DateTime
	public $duration; //int
	public $type; //string
	public $frame;

	function __construct($beginDate, $duration, $type, $frame){
		$this->beginDate = $beginDate;
		$this->duration = $duration;
		$this->type = $type;
		$this->frame = $frame;
		$this->endDate = new DateTime();
		$this->endDate->setTimestamp($this->beginDate->getTimestamp());
		$this->endDate->modify("+". $duration . " days");
	}

	public function getBeginDate(){
		return $beginDate->format("Y");
	}
	
	
	
}


?>
