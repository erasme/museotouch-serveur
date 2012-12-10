$(document).ready(function(){
    $('input[data-id=8]').datetimepicker({
    	dateFormat: "dd/mm/yy",
    	timeFormat: 'hh:mm:ss',
    	showSecond: true,
    	timeOnlyTitle: "Choisissez l'heure",
		timeText: '',
		hourText: 'Heure',
		minuteText: 'Minutes',
		secondText: 'Secondes',
		currentText: 'Maintenant',
		closeText: 'Valider',
		monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
		dayNamesMin: ['Di', 'Lu','Ma','Me','Je','Ve','Sa'],
		firstDay: 1
    });
});