Бот парсит https://freelansim.ru/user_rss_tasks/6Fpi1p32eMAPheTrxdyh и отправляет новые записи в телеграм канал.

1. Создать бота
2. Создать канал и добавить бота в админы
3. Для отправки сообщений (https://api.telegram.org/bot<token>/НАЗВАНИЕ_МЕТОДА)
4. В файле setting_for_api.php установить свои значения
5. В /etc/crontab добавить "*/15 * * * * www-data cd /var/www/путь_до_скрипта/ && php index_telegram_bot.php > /var/www/..../log.txt"
