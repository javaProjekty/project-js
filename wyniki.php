




<?php include('header.php') ?>
<div id='wyniki'>
</div>
 <a id="download" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Pobierz Plik</a>
 <a target='_blank' id="print" class="btn btn-secondary btn-lg active" role="button" aria-pressed="true">Drukuj</a>

<script> 

var url = new URL(window.location.href)

var weight = parseFloat(url.searchParams.get("weight"));
var bmi = parseFloat(url.searchParams.get("bmi"));
var phase = url.searchParams.get("phase");
var frequency = url.searchParams.get("frequency");

var NUMBER_OF_MACROCYCLES = 2;
var MIN_BMI_FOR_OVERWEIGHT = 25;



if(phase == null){
phase = "first";
}
if(bmi>MIN_BMI_FOR_OVERWEIGHT){
phase="third";
}
function translatePhaseName(phase){
	if(phase == 'first')
		return 'Trening na wzrost siły';
	if(phase == 'second')
		return 'Trening na Przyrost masy mięśniowej';
	if(phase == 'third')
		return  'Trening redukcyjny';
	if(phase == 'break')
		return 'Faza odpoczynkowa';
}

function getsNumberOfWeeksForPhases(frequency){
	if(frequency=="one"){
	return 6;
	}
	return Math.round(Math.random()*2+4)

};

function getNamesOfPhases(phase,bmi,numberOfPhases){
	var names = [];
	if(bmi>MIN_BMI_FOR_OVERWEIGHT){
		
		for(var a =0; a<numberOfPhases; a++){
			names.push("third","first","second");
		}
		names.push('third');
		
	
	}
	else{
		if(phase == "first"){
			for(var a =0; a<numberOfPhases; a++)
				names.push("first","second", "third");
			
		}
		else if(phase == "second"){
			for(var a =0; a<numberOfPhases; a++)
				names.push("second", "third","first");
			names.pop();
		}
		else{
			for(var a =0; a<numberOfPhases; a++)
				names.push("third","first","second");
			names.splice(-2,2);
		}
		
	}
	return names;
		

	
};



function createWeeks(phase, numberOfWeeks){
	var weeks = [];
	if(phase == "first"){
		
		var maxLoad = 85
		var series = 4;
		var breakTime = 4;
		var repetition = 6;
		for(var i = 1; i<=numberOfWeeks; i++){
			weeks.push({no:i,breakTime, maxLoad, series:Math.round(series), repetition});
			maxLoad+=2;
			series+=0.49*i
			if(series>6)
				series=6;
			breakTime=breakTime-0.5*(i-1);
			if(breakTime<3)
				breakTime=3;
			repetition--;
		}
		
	}
	if(phase == "second"){
		var maxLoad = 75
		var series = 4;
		var breakTime = 0.75;
		var repetition = 10;
		for(var i = 1; i<=numberOfWeeks; i++){
			weeks.push({no:i,breakTime, maxLoad, series:Math.round(series), repetition});
			maxLoad+=2;
			series-=0.49*i
			if(series<2)
				series=2;
			breakTime+=0.25;
			
			
		}
		
	}
	
	if(phase == "third"){
		var maxLoad = 60
		var series = 4;
		var breakTime = 0.5;
		var repetition = 17;
		for(var i = 1; i<=numberOfWeeks; i++){
			weeks.push({no:i,breakTime, maxLoad, series:Math.round(series), repetition});
			maxLoad+=3;
			series+=0.49*i
			if(series>6)
				series=6;
			repetition-=2*(i-1);
			if(repetition<13)
				repetition=13;
			
			
			
		}
		
	}
	
	return weeks;
	
}

function renderMacroCycle(phases) {
	var html = ['<table class="table"> '];
	for (var phase of phases) {
		html.push('<tr>');
		html.push(translatePhaseName(phase.name));
		
		html.push('<table class="table">');
			html.push('<tr><th>Tydzien</th><th>Czas Przerwy [s]</th><th>%1CM</th><th>Liczba Serii</th><th>Liczba Powtórzeń</th></tr>');
		for (var week of phase.weeks) {
			html.push('<tr>');
			html.push(`<td>${week.no}</td><td>${week.breakTime}</td><td>${week.maxLoad}</td><td>${week.series}</td><td>${week.repetition}</td>`);
			html.push('</tr>');
		}
		html.push('</table>');
		html.push('</tr>');
	}
	
	html.push('</table>');
	
	return html.join('');
}


function createMacroCycle(phase,bmi,numberOfPhases,frequency){
	
	var phases = [];
	var names = getNamesOfPhases(phase,bmi,numberOfPhases);
	for(var name of names){
		
		phases.push({
		   name,
		   weeks: createWeeks(name,getsNumberOfWeeksForPhases(frequency))
		});
		
		phases.push({
			name:"break",
			weeks: [{no:1,breakTime:1,maxLoad:50,series:2, repetition:15}]
		});
		
	}
	phases.pop();
	return phases;
}

window.addEventListener('load', () => {
	var marcoCyclePhases = createMacroCycle(phase,bmi,NUMBER_OF_MACROCYCLES,frequency);
	document.getElementById('print').href='dodruku.php?html='+escape(renderMacroCycle(marcoCyclePhases));
	var file = new Blob([renderMacroCycle(marcoCyclePhases)], {type: "text/html"});
	document.getElementById('download').download='tabelka.html';
	document.getElementById('download').href=URL.createObjectURL(file);
	document.getElementById('wyniki').innerHTML=renderMacroCycle(marcoCyclePhases);
	
});

function test(a, b) {
	a = JSON.stringify(a);
	b = JSON.stringify(b);
   if (a !== b) {
	console.error(`${a} is not equal ${b}`);
   } else {
	console.log('test OK');
   }
}


test(getNamesOfPhases("first",10, 1), ['first', 'second', 'third']);
test(getNamesOfPhases('first',30, 1), ['third', 'first', 'second', 'third']);
test(getNamesOfPhases("second", 10, 2), [ 'second', 'third','first', 'second', 'third']);
test(getNamesOfPhases('second', 30, 2), ['third', 'first', 'second', 'third','first', 'second', 'third']);
test(getNamesOfPhases("third", 10, 2), [  'third','first', 'second', 'third']);



test(createWeeks('first',5),[
	{no:1,breakTime:4,maxLoad:85,series:4, repetition:6},
	{no:2,breakTime:4,maxLoad:87,series:4, repetition:5},
	{no:3,breakTime:3.5,maxLoad:89,series:5, repetition:4},
	{no:4,breakTime:3,maxLoad:91,series:6, repetition:3},
	{no:5,breakTime:3,maxLoad:93,series:6, repetition:2},
	
]);

test(createWeeks('second',5),[
	{no:1,breakTime:0.75,maxLoad:75,series:4, repetition:10},
	{no:2,breakTime:1,maxLoad:77,series:4, repetition:10},
	{no:3,breakTime:1.25,maxLoad:79,series:3, repetition:10},
	{no:4,breakTime:1.5,maxLoad:81,series:2, repetition:10},
	{no:5,breakTime:1.75,maxLoad:83,series:2, repetition:10},
	
]);

test(createWeeks('third',5),[
	{no:1,breakTime:0.5,maxLoad:60,series:4, repetition:17},
	{no:2,breakTime:0.5,maxLoad:63,series:4, repetition:17},
	{no:3,breakTime:0.5,maxLoad:66,series:5, repetition:15},
	{no:4,breakTime:0.5,maxLoad:69,series:6, repetition:13},
	{no:5,breakTime:0.5,maxLoad:72,series:6, repetition:13},
	
]);

 </script>
 

 
<?php include('footer.php') ?>
 
 