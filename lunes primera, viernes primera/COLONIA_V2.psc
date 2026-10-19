Proceso COLONIA_VACACIONES
	Definir edad, anio Como Entero;
	Escribir 'Ingrese el año calendario: ';
	Leer anio;
	Escribir 'Ingrese la edad del niño: ';
	Leer edad;
	Para edad=edad Hasta 13 Con Paso 1 Hacer
		Escribir 'Con ', edad, ' de edad en ', anio;
		anio = anio + 1;
	FinPara
	
	Escribir 'El ultimo año que puede anotarse para la colonia es en ', (anio - 1);
FinProceso