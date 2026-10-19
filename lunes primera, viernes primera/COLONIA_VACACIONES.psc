Proceso COLONIA_VACACIONES
	Definir edad, anio Como Entero;
	Escribir 'Ingrese el año calendario: ';
	Leer anio;
	Escribir 'Ingrese la edad del niño: ';
	Leer edad;
	Mientras edad < 14
		Escribir 'Con ', edad, ' de edad en ', anio;
		edad = edad + 1;
		anio = anio + 1;
	FinMientras
	Escribir 'El último año que puede anotarse para la colonia es en ', (anio - 1);
FinProceso