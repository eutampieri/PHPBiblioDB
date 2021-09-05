# PHP BiblioDB
[![Build Status](https://status.continuousphp.com/git-hub/eutampieri/PHPBiblioDB?token=832985bb-3510-4872-ab91-435951b5a04a)](https://continuousphp.com/git-hub/eutampieri/PHPBiblioDB)

## Requirements
<!--* Yandex API key, available at https://translate.yandex.com/developers/keys. It has to be put in `res/yandexAPIKey.txt`.-->

## DataBase structure:

The database consists in an SQLITE file containing four tables.

### Libri

This table contains books. It has these columns:
* ISBN (text)
* Titolo (text)
* Autore (text)
### Copie

This table contains copies of a book.
* ID (text)
* ISBN (text)
* Posizione (text)
* Disponibilita (0:in prestito, 1:disponibile)
* DataPrestito (YYYY-MM-DD)
* UtentePrestito (text)

### Utenti

This table contains users. It has theese columns:
* Utente (text)
* Password (bcrypt encoded)
* Master (bool)

### Sessioni

This table contains sessions. It can be used for logging purposes. It has theese columns:
* Token (text)
* IP (text)
* Scadenza (YYYY-MM-DD H:i:s)
* Utente (text)

### Iscritti

This table contains library users. It has theese columns:
* ID (text)
* RFID (text)
* Nome (text)
* Cognome (text)

## Caching
To save time and load covers quickly, create a file named `covers.json` in the
folder in which is contained `index.php` with the following content:
```
{}
```
