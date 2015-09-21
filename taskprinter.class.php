<?

class TaskPrinter {
	private $language;
	private $months;
	private $months_r;
	private $year;
	private $year_r;
	private $declaration_begin;
	private $quarter1;
	private $quarter2;
	private $quarter3;
	private $quarter33;
	private $quarter4;
	private $half;
	private $esv_begin;
	private $en_begin;
	private $before;
	
	function __construct($language){
		$this->language = $language;
		if($language=="rus"){
			$this->months = array("XXX", "январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь");
			$this->months_r = array("XXX", "января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
			$this->year = " год";
			$this->year_r = " года";
			$this->declaration_begin = "Сдать налоговую декларацию за ";
			$this->quarter1 = "I квартал ";
			$this->quarter2 = "II квартал ";
			$this->quarter3 = "III квартал ";
			$this->quarter33 = "III квартала ";
			$this->quarter4 = "IV квартал ";
			$this->half = "I полугодие ";
			$this->esv_begin = "Оплатить ЕСВ за ";
			$this->en_begin = "Оплатить ЕН за ";
			$this->before = "до ";
			
			
		// Украинский==================================================
		//=============================================================
		//=============================================================	
		}elseif($language=="ukr"){
			$this->months = array("XXX", "ciчень", "лютий", "березень", "квiтень", "травень", "червень", "липень", "серпень", "вересень", "жовтень", "листопад", "грудень");
		
			$this->months_r = array("XXX", "ciчня", "лютого", "березня", "квiтня", "травня", "червня", "липня", "серпня", "вересня", "жовтня", "листопада", "грудня");
			
			$this->year = " рiк";
			$this->year_r = " року";
			
			$this->declaration_begin = "Подати податкову декларацiю за ";
			$this->quarter1 = "I квартал ";
			$this->quarter2 = "II квартал ";
			$this->quarter3 = "III квартал ";
			$this->quarter33 = "III квартали ";
			$this->quarter4 = "IV квартал ";
			$this->half = "I пiврiччя ";
			$this->esv_begin = "Сплатити ЄСВ за ";
			$this->en_begin = "Сплатити ЄП за ";
			$this->before = "до ";
		}
		
		
	}
	
	
	function taskBody($task){
		//Определяем тип задачи
		if($task->type=="declaration"){
		$taskString = $this->declaration_begin;
			if($task->frame=="year"){
				$taskString .= ($task->beginDate->format('Y')-1). $this->year; //Отнимаем еденицу, потому что декларация за прошлый год
			}
			else { //накопительная декларация за кварталы
				if($task->beginDate->format('m')=="01"){
					$taskString .= ($task->beginDate->format('Y')-1). $this->year; 
				}
				elseif($task->beginDate->format('m')=="04"){
					$taskString .= $this->quarter1. $task->beginDate->format('Y') . $this->year_r;
				}
				elseif($task->beginDate->format('m')=="07"){
					$taskString .= $this->half. $task->beginDate->format('Y') . $this->year_r;
				}
				elseif($task->beginDate->format('m')=="10"){
					$taskString .= $this->quarter33. $task->beginDate->format('Y') . $this->year_r;
				}
			}
		}
		elseif($task->type=="esv") {
			$taskString = $this->esv_begin;
			if($task->frame=="month"){
				$month = $task->beginDate->format('m')+0;
				if($task->beginDate->format('m')=="01"){
					$taskString .= $this->months[12]." " . ($task->beginDate->format('Y')-1). $this->year_r;
				}else {
					$taskString .= $this->months[$month]." " . $task->beginDate->format('Y'). $this->year_r;
				}
			}
			else { //оплата за кварталы
				if($task->beginDate->format('m')=="01"){
					$taskString .= $this->quarter4. ($task->beginDate->format('Y')-1). $this->year_r; 
				}
				elseif($task->beginDate->format('m')=="04"){
					$taskString .= $this->quarter1. $task->beginDate->format('Y') . $this->year_r;
				}
				elseif($task->beginDate->format('m')=="07"){
					$taskString .= $this->quarter2. $task->beginDate->format('Y') . $this->year_r;
				}
				elseif($task->beginDate->format('m')=="10"){
					$taskString .= $this->quarter3. $task->beginDate->format('Y') . $this->year_r;
				}
			}
		}
		elseif($task->type=="en") {
			$taskString = $this->en_begin;
			if($task->frame=="month"){
				$month = $task->beginDate->format('m')+0;
				$taskString .= $this->months[$month]." " . $task->beginDate->format('Y'). $this->year_r;		
			}
			else { //оплата за кварталы
				if($task->beginDate->format('m')=="01"){
					$taskString .= $this->quarter4. ($task->beginDate->format('Y')-1). $this->year_r; 
				}
				elseif($task->beginDate->format('m')=="04"){
					$taskString .= $this->quarter1. $task->beginDate->format('Y') . $this->year_r;
				}
				elseif($task->beginDate->format('m')=="07"){
					$taskString .= $this->quarter2. $task->beginDate->format('Y') . $this->year_r;
				}
				elseif($task->beginDate->format('m')=="10"){
					$taskString .= $this->quarter3. $task->beginDate->format('Y') . $this->year_r;
				}
			}
		}
		return $taskString;
	}
	function printTaskBody($task){
		echo $this->taskBody($task);
	}
	
	function taskDeadline($task){
		if($task->type=="declaration"){
			$deadlineDate = $task->endDate;
			$month = $deadlineDate->format('m')+0;
			$deadlineString = $this->before. $deadlineDate->format("d"). " ".$this->months_r[$month]. " ". $deadlineDate->format("Y"). $this->year_r;
			
		}
		elseif($task->type=="esv"){
			$deadlineDate = $task->endDate;
			$month = $deadlineDate->format('m')+0;
			$deadlineString = $this->before. $deadlineDate->format("d"). " ".$this->months_r[$month]. " ". $deadlineDate->format("Y"). $this->year_r;
			
		}
		elseif($task->type=="en"){
			if($task->frame=="quarter"){
				$deadlineDate = $task->endDate;
				$month = $deadlineDate->format('m')+0;
				$deadlineString = $this->before. $deadlineDate->format("d"). " ".$this->months_r[$month]. " ". $deadlineDate->format("Y"). $this->year_r;
			}
			elseif($task->frame=="month"){
			$deadlineDate = $task->endDate;;
			$month = $deadlineDate->format('m')+0;
			$deadlineString = $this->before. $deadlineDate->format("d"). " ".$this->months_r[$month]. " ". $deadlineDate->format("Y"). $this->year_r;
			}
		}
		return  $deadlineString;
	
	}
	
	function printTaskDeadLine($task){
		echo $this->taskDeadline($task);
	}
	
	public function taskBegin($task){
		return $task->beginDate->format("d.m.Y");
	}
	
	public function printTaskBegin($task){
		echo $this->taskBegin($task);
	}
	
}
?>
