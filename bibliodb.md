<meta name="viewport" content="width=device-width, initial-scale=1.0">
Specifiche BiblioDB.json
========================
*BiblioDB.json* è composto da un array di dizionari:
* Stato titolo (*dict[0]*):
	* Se non è presente la chiave corrispondente, il titolo non è mai stato prestato
	* Se il valore è 1, il titolo è disponibile
	* Se il valore è 0, il titolo è in prestito
* Posizione dell'ISBN (*dict[1]*)
* ISBN dato il titolo (*dict[2]*, il titolo è la chiave. Dizionario mantenuto per retrocompatibilità)
* Titolo dell'ISBN (*dict[3]*)
* Autore dell'ISBN (*dict[4]*)
* Attuale proprietario dell'ISBN (*dict[6]*)
* Numero massimo di giorni di prestito (*dict[7]*,**int**)
* Data del prestito nel formato DD/MM/AAAA (*dict[8]*)
