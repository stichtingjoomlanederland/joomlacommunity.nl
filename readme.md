# joomlacommunity.nl

Het Nederlandstalige Joomla!-portal

## Logo's en banners 
Logo's en banners van JoomlaCommunity zijn verwerkt op https://github.com/stichtingjoomlanederland/media
Wensen en issues met betrekking tot logo's en banners graag als issue verwerken op bovengenoemde repo. 

## Uitrol site op Byte server

### Git repo locaties
- Live: `/home/users/xxxxftp/git/live`
- Staging: `/home/users/xxxxftp/git/staging`

### Symlink aanmaken
In plaats van de standaard domein mappen op de Byte server maken we een symlink aan naar de Git repo locaties
- Live: `ln -s git/live/public_html domeinnaam.nl`
- Staging: `ln -s git/staging/public_html test.domeinnaam.nl`

## Installatie instructies
1. `git clone` uitvoeren van de repository
2. Kopiëer en hernoem en verplaats, indien nodig, het bestand `htaccess.perfect.txt` in de root directory naar `.htaccess` in `/public_html/` 
3. Kopiëer en hernoem en verplaats het bestand `configuration.php.dist` in de root directory naar `configuration.php` in `/public_html/`
4. Vul in `configuration.php`, indien nodig, de databasegegevens in.
5. Vul in `configuration.php` de paden naar de `tmp` en `administrator/logs` map in, afhankelijk van jouw werkomgeving
6. [Installeer yarn globally als je dat nog niet hebt](https://yarnpkg.com/en/docs/install)
7. Run `yarn install`.

## Images
Images worden tijdens development mee gecommit naar de repository. Zodra er een test server of live server is mag de images folder worden geleegd en mag `#/public_html/images/*` in `.gitignore` naar `/public_html/images/*` worden aangepast.

## Useful links
Useful links during frontend development
- SVG files for Font-Awesome icons: https://github.com/encharm/Font-Awesome-SVG-PNG/
- optimaliseren van je SVG bestanden: https://jakearchibald.github.io/svgomg/
- optimaliseren van je SVG bestanden: http://petercollingridge.appspot.com/svg-optimiser/

## Fonts
- Google Fonts: https://fonts.google.com/ 
- https://google-webfonts-helper.herokuapp.com/fonts
- convert to base64 for localfont: https://jaicab.com/localFont/ 

## Icons
- https://github.com/danleech/simple-icons
- https://github.com/iconic/open-iconic
- https://github.com/encharm/Font-Awesome-SVG-PNG/tree/master/black/svg

### NGROK :: Secure tunnels to localhost
website: https://ngrok.com/
``` 
$ ngrok http -auth='perfect:webteam' -host-header=example.dev 80
```
Hiermee kun je dan ```http://<randomnumber>.ngrok.io``` met htaccess login perfect:webteam openen in je browser en de site zien waarmee de developer aan de andere kant bezig is. 

## Virtualbox
- Via de site http://modern.ie kun je via Tools > Virtual Machines een virtuele machine installeren, zodat je via je Mac toch IE11 en Edge kunt draaien
- Direct na installeren van de virtuele machine en voor het opstarten ervan een snapshot maken. (dit is handig, omdat de VM slechts een trial versie is van 90dgn)
