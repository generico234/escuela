Proceso Semaforo
	// Declaración de variables
	Definir luzRoja Como Logico;
	Definir luzAmarilla Como Logico;
	Definir luzVerde Como Logico;
	Definir tiempo Como Entero;
	Definir colorActual Como Entero;
	// inicialización de variables con asignación de valores booleanos y entero
	luzRoja = Falso;
	luzAmarilla = Falso;
	luzVerde = Falso;
	tiempo = 0;
	// lectura de datos mediante teclado (entrada estandar)
	Escribir 'Seleccione el color actual: 1 Rojo - 2 Amarillo - 3 Verde';
	Leer colorActual;
	// estructura Según (Switch), de acuerdo al valor de colorActual se accederá a alguna de las 3 opciones
	Segun colorActual Hacer
		1:
			luzRoja = Verdadero;
			luzAmarilla = Falso;
			luzVerde = Falso;
		2:
			luzRoja = Falso;
			luzAmarilla = Verdadero;
			luzVerde = Falso;
		3:
			luzRoja = Falso;
			luzAmarilla = Falso;
			luzVerde = Verdadero;
	FinSegun
	// Si la variable contiene el dato Verdadero mostrará un mensaje en pantalla...
	Si luzRoja=Verdadero Entonces
		Escribir 'Esperar el permiso de paso';
	FinSi
	// Si la variable contiene el dato Verdadero mostrará un mensaje en pantalla...
	Si luzAmarilla=Verdadero Entonces
		Escribir 'Precaución, en breve cambio de color';
	FinSi
	// Si la variable contiene el dato Verdadero mostrará un mensaje en pantalla...
	Si luzVerde=Verdadero Entonces
		Escribir 'Circule';
	FinSi
FinProceso