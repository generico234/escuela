Proceso MAQUINA_CAFE
	Definir seleccion Como Entero;
	Escribir 'SELECCIONE LA BEBIDA QUE DESEA:';
	Escribir '1.- CAFÉ SIMPLE';
	Escribir '2.- CAFÉ LARGO';
	Escribir '3.- CAFÉ CORTADO';
	Escribir '4.- CAFÉ CON LECHE';
	
	Escribir '5.- CHOCOLATADA';
	Escribir '6.- LÁGRIMA';
	Leer seleccion;
	Segun seleccion
		1: Escribir 'Usted seleccionó CAFÉ SIMPLE';
		2: Escribir 'Usted seleccionó CAFÉ LARGO';
		3: Escribir 'Usted seleccionó CAFÉ CORTADO';
		4: Escribir 'Usted seleccionó CAFÉ CON LECHE';
		5: Escribir 'Usted seleccionó CHOCOLATADA';
		6: Escribir 'Usted seleccionó LÁGRIMA';
		De Otro Modo:
			Escribir 'Usted seleccionó una opción no disponible.';
	FinSegun
FinProceso