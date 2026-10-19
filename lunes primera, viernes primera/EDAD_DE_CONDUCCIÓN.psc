Proceso REQUISITO_EDAD_CARNET
	Definir edad Como Entero;
	Escribir 'Ingrese la edad del solicitante:';
	Leer edad;
	Si edad >= 18 Entonces
		Escribir 'Puede iniciar el registro';
	SiNo
		Escribir 'No cuenta con la edad mínima para realizar la solicitud';
	FinSi
FinProceso