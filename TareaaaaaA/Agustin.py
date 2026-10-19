nonvre =str(input("ingrese su nombre: "))
apellido= str (input("ingrese su apellido: "))
edad = str(input("ingrese su edad: "))
lista = [nonvre,apellido,edad]
with open("noeselmismoarchivo.txt", "a") as estesieselarchivo:
    estesieselarchivo.write(str(lista))
