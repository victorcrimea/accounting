<?
require_once "task.class.php";
require_once "taskprinter.class.php";

class Accountant {
	private $group; //int
	private $period_from; //string
	private $period_to; // string
	private $begin_year; // int
	private $end_year; // int
	private $esv_frame; //string
	private $language; //string
	private $holidays; //array of strings
	
	function __construct(){
		$this->holidays = array();
		$this->holidays[] = "01-01"; // новый год
		$this->holidays[] = "01-07"; // рождество
		$this->holidays[] = "03-08"; // международный женский день
		$this->holidays[] = "05-01"; // день солидарности трудящихся
		$this->holidays[] = "05-02"; // день солидарности трудящихся
		$this->holidays[] = "05-09"; // день победы
		$this->holidays[] = "06-28"; // день конституции
		$this->holidays[] = "08-24"; // день независимости
		$this->holidays[] = "10-14"; // день защитника украины
		//Проверка на пасху и троицу будет позже
	}
	
	public function setGroup($group){
		$this->group = $group;
	}

	public function setPeriod($period_from, $period_to){
		$this->period_from= new DateTime($period_from);
		$this->period_to= new DateTime($period_to);
		//Узнаю год начала периода
		$this->begin_year = $this->period_from->format("Y");
		//Узнаю год конца периода
		$this->end_year = $this->period_to->format("Y");
		
	}

	public function setEsvFrame($esv_frame){
		$this->esv_frame=$esv_frame;
	}
	
	function setLanguage($language){
		$this->language = $language;
	}
	
	static function taskSort($e1, $e2){ //Функция сортирует объекты Task по возрастанию даты
		if($e1->endDate->getTimestamp() > $e2->endDate->getTimestamp()) return 1;
		elseif($e1->endDate->getTimestamp() < $e2->endDate->getTimestamp()) return -1;
		else return 0;
	}

	public function printRawTasks(){
		$tasks = print_r($this->getTasks(), true);
		echo str_replace("\n", "<br/>", str_replace("    ", "&nbsp;&nbsp;&nbsp;&nbsp;", $tasks));
	}
	
	public function printTasks(){
		$tasks = $this->getTasks();
		$printer = new TaskPrinter($this->language);
		
		foreach($tasks as $key=>$task){
			$printer->printTaskBegin($task);
			echo " - ";
			$printer->printTaskBody($task);
			echo " ";
			$printer->printTaskDeadLine($task);
			echo "<br>";
		}
		
	}
	public function printTasksJSON(){
		$tasks = $this->getTasks();
		$printer = new TaskPrinter($this->language);
		$json=array();
		foreach($tasks as $key=>$task){
			$json[$key]["date"]= $printer->taskBegin($task);
			
			$json[$key]["body"]=$printer->taskBody($task);
			
			$json[$key]["deadline"]=$printer->taskDeadLine($task);
		}
		echo json_encode($json, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE);
	}

	private function addDeclaration($begin_year, $end_year, $frame){
		$tasks= array();
		if($frame=="year"){
			for($i=0; $i<=($end_year-$begin_year); $i++){
				$date = new DateTime(($begin_year+$i)."-01-01");
				$tasks[] = new Task($date, 40, "declaration", "year");
			}
		}
		elseif($frame=="quarter"){
			for($i=0; $i<=($end_year-$begin_year); $i++){
				$date = new DateTime(($begin_year+$i)."-01-01");
				$tasks[] = new Task($date, 40, "declaration", "quarter");
				$date = new DateTime(($begin_year+$i)."-04-01");
				$tasks[] = new Task($date, 40, "declaration", "quarter");
				$date = new DateTime(($begin_year+$i)."-07-01");
				$tasks[] = new Task($date, 40, "declaration", "quarter");
				$date = new DateTime(($begin_year+$i)."-10-01");
				$tasks[] = new Task($date, 40, "declaration", "quarter");
			}
		}
		return $tasks;
	}
	
	private function addESV($begin_year, $end_year, $frame){
		$tasks = array();
		if($frame=="month"){
			for($i=0; $i<=($end_year-$begin_year); $i++){
				for($month=1; $month<=12; $month++){
					$date = new DateTime(($begin_year+$i)."-". $month ."-01");
					$tasks[] = new Task($date, 19, "esv", "month");
				}
			}
		}
		elseif($frame=="quarter"){
			for($i=0; $i<=($end_year-$begin_year); $i++){
				$date = new DateTime(($begin_year+$i)."-01-01");
				$tasks[] = new Task($date, 19, "esv", "quarter");
				$date = new DateTime(($begin_year+$i)."-04-01");
				$tasks[] = new Task($date, 19, "esv", "quarter");
				$date = new DateTime(($begin_year+$i)."-07-01");
				$tasks[] = new Task($date, 19, "esv", "quarter");
				$date = new DateTime(($begin_year+$i)."-10-01");
				$tasks[] = new Task($date, 19, "esv", "quarter");
			}
		}
		return $tasks;
	}
	
	private function addEN($begin_year, $end_year, $frame, $duration){
		$tasks = array();
		if($frame=="month"){
			for($i=0; $i<=($end_year-$begin_year); $i++){
				for($month=1; $month<=12; $month++){
					$date = new DateTime(($begin_year+$i)."-". $month ."-01");
					$tasks[] = new Task($date, $duration, "en", "month");
				}
			}
		}
		elseif($frame=="quarter"){
			for($i=0; $i<=($end_year-$begin_year); $i++){
				$date = new DateTime(($begin_year+$i)."-01-01");
				$tasks[] = new Task($date, $duration, "en", "quarter");
				$date = new DateTime(($begin_year+$i)."-04-01");
				$tasks[] = new Task($date, $duration, "en", "quarter");
				$date = new DateTime(($begin_year+$i)."-07-01");
				$tasks[] = new Task($date, $duration, "en", "quarter");
				$date = new DateTime(($begin_year+$i)."-10-01");
				$tasks[] = new Task($date, $duration, "en", "quarter");
			}
		}
		return $tasks;
	}
	

	private function getTasksGroup1(){
		//Поставить задачи с декларациями
		$DeclarationTasks = $this->addDeclaration($this->begin_year, $this->end_year, "year");
		
		//Поставить задачи оплаты ЕСВ
		$ESVTasks = $this->addESV($this->begin_year,$this->end_year, $this->esv_frame);
		//Поставить задачи оплаты ЕН
		$ENTasks = $this->addEN($this->begin_year, $this->end_year, "month", 19);
		
		$tasks = array_merge($DeclarationTasks, $ESVTasks, $ENTasks);
		return $tasks;
	}
	
	private function getTasksGroup2(){
		return $this->getTasksGroup1();
	}
	private function getTasksGroup3(){
		//Поставить задачи с декларациями
		$DeclarationTasks = $this->addDeclaration($this->begin_year, $this->end_year, "quarter");
		
		//Поставить задачи оплаты ЕСВ
		$ESVTasks = $this->addESV($this->begin_year,$this->end_year, $this->esv_frame);
		//Поставить задачи оплаты ЕН
		$ENTasks = $this->addEN($this->begin_year, $this->end_year, "quarter", 50);
		
		$tasks = array_merge($DeclarationTasks, $ESVTasks, $ENTasks);
		return $tasks;
	}
	private function getTasksGroup4(){
		//Поставить задачи с декларациями
		$DeclarationTasks = $this->addDeclaration($this->begin_year, $this->end_year, "quarter");
		
		//Поставить задачи оплаты ЕН
		$ENTasks = $this->addEN($this->begin_year, $this->end_year, "quarter", 50);
		
		$tasks = array_merge($DeclarationTasks, $ENTasks);
		return $tasks;
	}
	private function getTasksGroup5(){
		return $this->getTasksGroup3();
	}
	private function getTasksGroup6(){
		return $this->getTasksGroup4();
	}

	private function filter($tasks){
		$begin_timestamp = $this->period_from->getTimestamp();
		$end_timestamp = $this->period_to->getTimestamp();
		$task_out = array();
		foreach ($tasks as $key=>$task){
			$task_timestamp = $task->beginDate->getTimestamp();
			if($task_timestamp<=$end_timestamp && $task_timestamp>=$begin_timestamp){
				$task_out[]=$task;
			}
		}
		return $task_out;
	}
	
	public function isHoliday($date){
		$dayOfWeek = $date->format("w"); // получаю день недели
		
		if($dayOfWeek==0 ||$dayOfWeek==6) return true;
		
		$stringDate = $date->format("m-d");
		if(array_search($stringDate, $this->holidays)) return true; //Если дата принадлежит к списку статичных праздников
		
		//Пасха и троица и так всегда в воскресенье
		return false;//  только если это не выходной и не праздник
	}
	
	private function holidayCorrection($tasks){
		$tasks_out= array();
		foreach($tasks as $key=>$task){
			
			while($this->isHoliday($task->endDate)){
				if($task->type=="declaration"){
					$task->endDate->modify("+1 day"); // подачу декларации переносим на день вперёд
					//echo "Y";
				}
				else{
					$task->endDate->modify("-1 day"); // оплату ЕСВ/ЕН переносим на день назад
					//echo "X";
				}
			}
			
			$duration = $task->endDate->diff($task->beginDate);
			$duration_days = $duration->format("%d");
			$task->duration = $duration_days;
			$tasks_out[] = $task;
		}
		
		return $tasks_out;
	}
	
	public function getTasks(){
		switch($this->group){
			case 1:
				$tasks = $this->getTasksGroup1();
				break;
			case 2:
				$tasks = $this->getTasksGroup2();
				break;
			case 3:
				$tasks = $this->getTasksGroup3();
				break;
			case 4:
				$tasks = $this->getTasksGroup4();
				break;
			case 5:
				$tasks = $this->getTasksGroup5();
				break;
			case 6:
				$tasks = $this->getTasksGroup6();
				break;
		}
		//Фильтрую все задачи, которые не вошли в запрошенный период
		$tasks= $this->filter($tasks);
		
		//Коррекция задач относительно выходных и праздников
		
		$tasks = $this->holidayCorrection($tasks);
		//Сортирую задачи по дате
		usort($tasks, get_class($this)."::taskSort");
		//Возврат задач
		return $tasks;
	}
}


?>
