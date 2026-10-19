color=int(input("ingrese el color del semaforo 1-verde 2-amarillo 3-rojo "))
colorverde=bool(False)
coloramarillo=bool(False)
colorrojo=bool(False)
if color==1:
    colorverde=bool(True)
    coloramarillo=bool(False)
    colorrojo=bool(False)
elif color==2:
    colorverde=bool(False)
    coloramarillo=bool(True)
    colorrojo=bool(False)
elif color==3:
    colorverde=bool(False)
    coloramarillo=bool(False)
    colorrojo=bool(True)
else:
    print ("invalido")
if colorverde==True:
    print("circule")
if coloramarillo==True:
    print("precaucion")
if colorrojo==True:
    print("espere")