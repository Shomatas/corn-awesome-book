# Запуск cron задачи, для вывода списка книг
## Установка и использование
Для запуска проекта необходимо склонировать данный репозиторий и запустить заранее проект awesome-book (https://gitlab.com/paff2/awesome-book)
```
git clone https://github.com/Shomatas/cron-awesome-book.git
```
Далее нужно запустить контейнеры
```
docker-compose up -d
```
Для загрузки сторонних библиотек используется пакетный менеджер composer. Утилита symfony хранит внутри себя реализацию composer. Установите пакеты внутри контейнера
```
docker-compose exec -it cron-awesome-books symfony composer install
```
Проект готов к запуску. Для запуска cron задачи необходимо внутри контейнера cron-awesome-books запустить команду cron
```
docker-compose exec -it cron-awesome-books cron
```
Теперь, каждый день в 9:00 и 18:00 cron будет запускать задачу, которая получает данные API /books из проекта awesome-book.
Данные сохраняются в папке response, с названием response.json
## Дополнительно
### Команда symfony
Консольная команда symfony: app:get-books, берет API /books из проекта awesome-book.
### Настройка cron задачи, внутри Dockerfile
Добавление cron задачи осуществляется через поднятие контейнера cron-awesome-books.
Для конфигурации задачи внутри Dockerfile, нужно открыть docker/php-fpm/Dockerfile.
Внутри файла, конфигурация ниже приведенной строки приведет к изменению cron задачи.
```
RUN (crontab -l ; echo "0 9,18 * * * /usr/local/bin/php /app/bin/console app:get-books") | crontab
```
После изменения строки, необходимо пересобрать контейнер.