
class Animal:
    def __init__(self, mamifero, patas, ojos, color_pelaje, vertebrado):
        self.mamifero = mamifero
        self.patas = patas
        self.ojos = ojos
        self.color_pelaje = color_pelaje
        self.vertebrado = vertebrado

    def nacer(self):
        print("El animal ha nacido")

    def comer(self):
        print("El animal está comiendo")

    def desplazarse(self):
        print("El animal se está desplazando")


class Tiburon(Animal):
    def __init__(self, ojos, vertebrado):
        super().__init__(
            mamifero=False,
            patas=0,
            ojos=ojos,
            color_pelaje=None,
            vertebrado=vertebrado
        )

    def nadar(self):
        print("El tiburón está nadando")


class Perro(Animal):
    def __init__(self, raza, color_pelaje):
        super().__init__(
            mamifero=True,
            patas=4,
            ojos=2,
            color_pelaje=color_pelaje,
            vertebrado=True
        )
        self.raza = raza

    def ladrar(self):
        print("miau")



perro1 = Perro("Labrador", "Negro")
perro2 = Perro("Caniche", "Blanco")
perro3 = Perro("Bulldog", "Marrón")

tiburon1 = Tiburon(2, True)
tiburon2 = Tiburon(3, True)
tiburon3 = Tiburon(2, True)


animales = [
    perro1,
    perro2,
    perro3,
    tiburon1,
    tiburon2,
    tiburon3
]

for animal in animales:
    animal.comer()