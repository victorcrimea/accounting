<?
 header('Content-Type: text/html; charset=utf-8');
?>
<html>
	<head>
		<script src='http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js'></script>
		<script>
			//alert("hello");
			function form_submit(){
				//$('#form').submit();
				//return;
				//alert("form_submit");
				var values = $('#form').serialize();
				$.ajax({
					type: "POST",
					url:"taxes.php", 
					data:values, 
					dataType: "json",
					success:function(result){
						
						$.each(result, function(key, val){
							$("#tasks").append("<tr><td>"+val.date+"</td><td>"+val.body+"</td><td>"+val.deadline+"</td></tr>")
						});
						;
					}
				});
				
			}
		</script>
	</head>
	
	<body>
			<h3>Планировщик задач</h3>
			<form id='form' action="taxes.php" method="POST">
			<table>
				<tr>
					<td>Группа</td>
					<td>
						<select name='group'>
							<option value='1'>Группа I</option>
							<option value='2'>Группа II</option>
							<option value='3'>Группа III</option>
							<option value='4'>Группа IV</option>
							<option value='5'>Группа V</option>
							<option value='6'>Группа VI</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Период</td>
					<td>
						<input type='date' name='period_from' placeholder='От'>
						<input type='date' name='period_to' placeholder='До'>
					</td>
				</tr>
				<tr>
					<td>Периодичность оплаты ЕСВ</td>
					<td>
						<input type='radio' name='esv_frame' value='month' checked='checked'>Ежемесячно
						<input type='radio' name='esv_frame' value='quarter'>Ежеквартально
						
					</td>
				</tr>
				<tr>
					<td>Язык</td>
					<td>
						<input type='radio' name='language' value='rus' checked='checked'>Русский
						<input type='radio' name='language' value='ukr'>Украинский
						
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<button onclick='form_submit(); return false;'>Показать</button>
					</td>
				</tr>
			</table>
			</form>
			<table id='tasks'>
			
			</table>
	</body>


</html>
