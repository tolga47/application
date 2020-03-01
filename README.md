# application
 Test Order API Project

Kurulum;

Öncelikle klasörümüzü sunucunun /application dizinine yüklüyoruz.
`docker-compose build` komutunu çalıştırıyoruz.

Daha sonra `docker-compose up -d` komutu ile projemizi developer modunda ayağa kaldırıyoruz.

`docker-compose exec php-fpm bash`

Komutunu çalıştırıyor ve 

`composer install`

ile kurulumu yapıyoruz.

Veritabanını kurmak için;
`bash install-clean.sh`
komutunu çalıştırıyoruz.

## Cache temizleme;
`docker-compose exec php-fpm bash`
`bash cacl.sh prod`
`bash cacl.sh dev`


## Test;
`docker-compose exec php-fpm bash`
`bash runtests.sh`
`bash runtests.sh debug`


## Postman dosyası klasörün içerisindedir.
