bibliotique=[
    ["L'Étranger", "Albert Camus", "1942"], 
["Le Petit Prince", "Antoine de Saint-Exupéry", "1943"],
]
def ajouter():
    nom=input("entrer le nom de livre")
    auteur=input("entrer le nom de auteur")
    annee=input("enter l annee d ecriture")
    ajr=[nom,auteur,annee]
    bibliotique.append(ajr )
    print("le livre",nom,"a ete ajouter avec succes")
    return
def afficher():
    if bibliotique==[]:
     print("auccun livre dans la bibliotheque")
    else:
       print("liste des livres disponibles")
       for i, valeur in enumerate (bibliotique, start=1): 
          print(i,".", valeur[0],"-",valeur[1],valeur[2])
       return
def recherche():
 mot_cle=input("entrez un mot-cle (titre au auteur)").lower()
 resultats=[]
 for livre in bibliotique:
    if mot_cle in livre [0].lower or mot_cle in livre [1].lower :
       resultats.append(livre)
       
    if resultats !=[]:
       for valeur in resultats:
          print(valeur[0],"-",valeur[1],valeur[2])
    else:
       print("aucun resultat trouver")
 
       return
def supprimer():
   afficher()
   if bibliotique==[]:
    choix = int(input("entrer le nemuro du livre a supprimer:"))
    if 1 <= choix <= len(bibliotique):
       livre_supprimr=bibliotique.pop(choix -1)
       print("le livre", livre_supprimr[0],"a ete supprime.")
   else :
    print("Numero invalide.")
    return

while True:                #hna loop obliger bch tbqa  t executi shhal mbghina ta ndero l break
 print("=====Menu======")
 print("1. Ajouter un livre : ")
 print("2. Afficher tous les livres : ")
 print("3. Rechercher un livre : ")
 print("4. Supprimer un livre : ")
 print("5. Quitter le programme : ")
 print("=====================")
 choix=input("choisir :")
 if choix =="1":
    ajouter()
 elif choix=="2":
   afficher()
 elif choix=="3":
   recherche()
 elif choix=="4":
  supprimer()
 elif choix=="5":
    print("Au revoir! merci d avoir utiliser la bibliotheque")
    break
 else:
    print("option invalide, essayez encore.")