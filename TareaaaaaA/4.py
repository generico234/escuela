from datetime import datetime

hola = str(datetime.now())
with open("2.txt", "a") as Archivo:
    Archivo.write(hola)
